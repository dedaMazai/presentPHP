<?php

namespace App\Services\Sales\Demand;

use App\Models\Contract\ContractService;
use App\Models\Sales\Deal;
use App\Models\Sales\Demand\Demand;
use App\Models\Sales\Demand\DemandBookingStatus;
use App\Models\Sales\Demand\DemandBookingType;
use App\Models\Sales\Demand\DemandState;
use App\Models\Sales\Demand\DemandStatus;
use App\Models\Sales\Demand\DemandType;
use App\Models\Sales\FamilyStatus;
use App\Models\Sales\OwnerType;
use App\Models\Sales\PaymentMode;
use App\Models\User\User;
use App\Services\Contract\ContractRepository;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\Sales\ArticleOrderRepository;
use App\Services\Sales\CharacteristicSaleRepository;
use App\Services\Sales\Customer\CustomerRepository;
use App\Services\Sales\Deal\DealService;
use App\Services\Sales\OwnerRepository;
use App\Services\Sales\OwnershipRepository;
use App\Services\Sales\PaymentPlanRepository;
use App\Services\Sales\Property\PropertyRepository;
use Carbon\Carbon;
use Exception;

/**
 * Class DemandRepository
 *
 * @package App\Services\Demand
 */
class DemandRepository
{
    public function __construct(
        private DynamicsCrmClient $dynamicsCrmClient,
        private PropertyRepository $propertyRepository,
        private ContractRepository $contractRepository,
        private PaymentPlanRepository $paymentPlanRepository,
        private CustomerRepository $customerRepository,
        private ArticleOrderRepository $articleOrderRepository,
        private OwnerRepository $ownerRepository,
        private CharacteristicSaleRepository $characteristicSaleRepository,
        private DealService $dealService,
        private OwnershipRepository $ownershipRepository,
    ) {
    }

    /**
     * @return Demand[]
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getDemands(User $user, ?string $parentId = ''): array
    {
        $data = $this->dynamicsCrmClient->getDemands($user);

        $demands = [];
        foreach ($data['demandList'] as $demandData) {
            if (!DemandType::tryFrom($demandData['demandType']['code'])) {
                continue;
            }

            if ($parentId && isset($demandData['demandMainId']) && $demandData['demandMainId'] == $parentId) {
                $demands[] = $this->makeDemand($demandData, $user);
            } elseif (!$parentId && !isset($demandData['demandMainId'])) {
                $demands[] = $this->makeDemand($demandData, $user);
            }
        }

        return $demands;
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getDemandsCountByStatus(User $user, DemandStatus $status): int
    {
        $data = $this->dynamicsCrmClient->getDemandsByStatus($user, $status);

        $demandsCount = 0;
        foreach ($data['demandList'] as $demandData) {
            if (!DemandType::tryFrom($demandData['demandType']['code'])) {
                continue;
            }

            $demandsCount++;
        }

        return $demandsCount;
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getDemandById(string $id, User $user): Demand
    {
        $data = $this->dynamicsCrmClient->getDemandById($id, $user);
        if (!DemandType::tryFrom($data['demandType']['code'])) {
            throw new NotFoundException();
        }

        return $this->makeDemand($data, $user);
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    private function makeDemand(array $data, User $user): Demand
    {
        $property = null;
        $propertyContracts = [];
        $paidBookingContract = null;
        if (isset($data['articleId'])) {
            $property = $this->propertyRepository->getById($data['articleId']);
            $propertyContracts = $this->contractRepository->getAllByPropertyId($data['articleId'], $user->crm_id);
            foreach ($propertyContracts as $propertyContract) {
                if ($propertyContract->getService() &&
                    ContractService::paidBooking()->equals($propertyContract->getService())
                ) {
                    $paidBookingContract = $propertyContract;
                }
                if ($paidBookingContract == null &&
                    ContractService::premium()->equals($propertyContract->getService())
                ) {
                    $paidBookingContract = $propertyContract;
                }
            }
        }
        $contract = null;
        if (isset($data['contractId'])) {
            $contract = $this->contractRepository->getById($data['contractId']);
        }
        $customer = null;
        if (isset($data['customerId'])) {
            $customer = $this->customerRepository->getById($data['customerId']);
        }
        $baseFinishVariant = null;
        if (isset($data['baseFinishVariant'])) {
            $baseFinishVariant = $this->characteristicSaleRepository
                ->makeCharacteristicSale($data['baseFinishVariant']);
        }

        $paymentPlans = [];
        if (isset($data['paymentPlans'])) {
            foreach ($data['paymentPlans'] as $paymentPlan) {
                $paymentPlans[] = $this->paymentPlanRepository->makePaymentPlan($paymentPlan);
            }
        }

        // phpcs:disable
        $ownership = null;
        $jointOwners = [];
        if (isset($data['jointOwners'])) {
            foreach ($data['jointOwners'] as $key => $jointOwner) {
                if ($jointOwner['id'] == $user->crm_id) {
                    $data['jointOwners'][$key]["isCustomer"] = true;
                } else {
                    $data['jointOwners'][$key]["isCustomer"] = false;
                }
            }

            $personalOwnerships = collect($data['jointOwners'])->where('ownerType.code', '=', OwnerType::personal()->value);
            $sharedOwnerships = collect($data['jointOwners'])->where('ownerType.code', '=', OwnerType::shared()->value);
            $jointOwnerships = collect($data['jointOwners'])->where('ownerType.code', '=', OwnerType::joint()->value);
            if ($personalOwnerships->count() != 0) {
                $ownership = $this->ownershipRepository->makeOwnership([
                    'code' => OwnerType::personal()->value,
                    'name' => OwnerType::personal()->label,
                    'message' => '',
                ]);
            } elseif ($sharedOwnerships->count() != 0) {
                $ownership = $this->ownershipRepository->makeOwnership([
                    'code' => OwnerType::shared()->value,
                    'name' => OwnerType::shared()->label,
                    'message' => '',
                ]);
            } elseif ($jointOwnerships->count() != 0) {
                $ownership = $this->ownershipRepository->makeOwnership([
                    'code' => OwnerType::joint()->value,
                    'name' => OwnerType::joint()->label,
                    'message' => '',
                ]);
            }

            if (isset($data['jointOwners'])) {
                $jointOwnersCount = count($data['jointOwners']);
                if ($jointOwnersCount == 1 && $ownership?->getCode() == 5) {
                    $data['jointOwners'][0]['label'] = 'Участник N1';
                } elseif ($jointOwnersCount == 2 && $ownership?->getCode() == 5) {
                    $fIsCustomer = $data['jointOwners'][0]['isCustomer'] ?? true;
                    $sIsCustomer = $data['jointOwners'][1]['isCustomer'] ?? true;

                    if (!$fIsCustomer) {
                        if (Carbon::parse($data['jointOwners'][0]['birthDate'])->age >= 18) {
                            $data['jointOwners'][0]['label'] = 'Участник N1';
                            $data['jointOwners'][1]['label'] = 'Доверенное лицо';
                        }

                        if (Carbon::parse($data['jointOwners'][0]['birthDate'])->age < 18) {
                            $data['jointOwners'][0]['label'] = 'Участник N1';
                            $data['jointOwners'][1]['label'] = 'Представитель ребенка';
                        }
                    }
                    if (!$sIsCustomer) {
                        if (Carbon::parse($data['jointOwners'][0]['birthDate'])->age >= 18) {
                            $data['jointOwners'][1]['label'] = 'Участник N1';
                            $data['jointOwners'][0]['label'] = 'Доверенное лицо';
                        }

                        if (Carbon::parse($data['jointOwners'][0]['birthDate'])->age < 18) {
                            $data['jointOwners'][1]['label'] = 'Участник N1';
                            $data['jointOwners'][0]['label'] = 'Представитель ребенка';
                        }
                    }
                } elseif ($jointOwnersCount == 2 && $ownership?->getCode() == 1) {
                    if ($data['jointOwners'][0]['isCustomer'] ?? null == true) {
                        $data['jointOwners'][0]['label'] = 'Участник N1';
                        $data['jointOwners'][1]['label'] = 'Участник N2';
                    } elseif ($data['jointOwners'][1]['isCustomer'] ?? null == true) {
                        $data['jointOwners'][1]['label'] = 'Участник N1';
                        $data['jointOwners'][0]['label'] = 'Участник N2';
                    }
                } elseif ($jointOwnersCount >= 2 && $ownership?->getCode() == 2) {
                    $ownershipForms = [];
                    foreach ($data['jointOwners'] as $index => $joinOwner) {
                        if (($joinOwner['isCustomer'] ?? false) && ($joinOwner['type']['code'] ?? 0) == 4) {
                            $ownershipForms[$index] = 'Представитель/Доверенное лицо';
                        } elseif (($joinOwner['isCustomer'] ?? false) && ($joinOwner['type']['code'] ?? 0) == 2) {
                            $ownershipForms[$index] = 'Участник N1';
                        } else {
                            $ownershipForms[$index] = 'Участник N' . ($index + 1);
                        }
                    }
                    foreach ($data['jointOwners'] as $key => $jointOwner) {
                        $data['jointOwners'][$key]['label'] = $ownershipForms[$key];
                    }
                }

                foreach ($data['jointOwners'] as $jointOwner) {
                    $jointOwners[] = $this->customerRepository->makeCustomer($jointOwner);
                }
            }
        }

        $articleOrders = [];
        if (isset($data['articleOrders'])) {
            foreach ($data['articleOrders'] as $articleOrder) {
                $articleOrders[] = $this->articleOrderRepository->makeArticleOrder($articleOrder);
            }
        }

        $articleOrders = [];
        $mainArticleOrder = null;
        if (isset($data['articleOrders'])) {
            foreach ($data['articleOrders'] as $articleOrderRaw) {
                $articleOrder = $this->articleOrderRepository->makeArticleOrder($articleOrderRaw);
                if (!$mainArticleOrder && $property && $articleOrder->getPropertyId() == $property->getId()) {
                    $mainArticleOrder = $articleOrder;
                }

                $articleOrders[] = $articleOrder;
            }
        }

        $characteristics = [];
        if (isset($data['characteristicSales'])) {
            foreach ($data['characteristicSales'] as $characteristic) {
                $characteristics[] = $this->characteristicSaleRepository->makeCharacteristicSale($characteristic);
            }
        }

        /** @var Deal $deal */
        $deal = Deal::firstWhere(['demand_id' => $data['id']]);
        $mortgageDemand = null;
        if ($deal?->mortgage_demand_id) {
            try {
                $mortgageDemand = $this->getDemandById($deal->mortgage_demand_id, $user);
            } catch (Exception) {
                $this->dealService->setMortgageDemandId($deal, null);
            }
        }

        if (isset($data['demandSubType']['code'])) {
            if ($data['demandSubType']['code'] == 1 || ((
                        $data['demandSubType']['code'] == 2 ||
                        $data['demandSubType']['code'] == 8 ||
                        $data['demandSubType']['code'] == 16) && (($data['reserveOpportunityName'] != null) &&
                        $data['contractReservPaymentStatus']['code'] == 1))) {
                $isMortgageOnlineAvailable = false;
            } else {
                $isMortgageOnlineAvailable = true;
            }
        } else {
            $isMortgageOnlineAvailable = true;
        }

        return new Demand(
            id: $data['id'],
            parentId: $data['demandMainId'] ?? null,
            number: $data['number'],
            type: DemandType::from($data['demandType']['code']),
            createdDate: isset($data['demandDate']) ? new Carbon($data['demandDate']) : null,
            bookingType: DemandBookingType::tryFrom($data['demandSubType']['code'] ?? ''),
            stepName: $data['stepName'] ?? null,
            beginDate: isset($data['beginDate']) ? new Carbon($data['beginDate']) : null,
            endDate: isset($data['endDate']) ? new Carbon($data['endDate']) : null,
            subject: $data['subject'] ?? null,
            lastName: $data['lastName'] ?? null,
            firstName: $data['firstName'] ?? null,
            middleName: $data['middleName'] ?? null,
            birthDate: isset($data['birthDate']) ? new Carbon($data['birthDate']) : null,
            phone: $data['phone'] ?? null,
            email: $data['email'] ?? null,
            addressId: $data['addressId'] ?? null,
            property: $property,
            contract: $contract,
            customer: $customer,
            jointOwners: $jointOwners,
            articleOrders: $articleOrders,
            paymentPlans: $paymentPlans,
            owner: isset($data['ownerObject']) ? $this->ownerRepository->makeOwner($data['ownerObject']) : null,
            description: $data['description'] ?? null,
            state: DemandState::tryFrom($data['state']['code'] ?? ''),
            status: DemandStatus::tryFrom($data['status']['code'] ?? ''),
            reserveOpportunityName: $data['reserveOpportunityName'] ?? null,
            bookingPaymentStatus: DemandBookingStatus::tryFrom($data['contractReservPaymentStatus']['code'] ?? ''),
            isLetterOfCredit: $data['isLetterOfCredit'] ?? null,
            isElectronicRegistration: $data['isElectronicRegistration'] ?? null,
            hypothecBankId: $data['hypothecBankId'] ?? null,
            letterOfCreditBankId: $data['letterOfCreditBankId'] ?? null,
            paymentMode: PaymentMode::tryFrom($data['paymentModeCode']['code'] ?? ''),
            finishingSalesStart: isset($data['finishingSalesStart']) ? new Carbon($data['finishingSalesStart']) : null,
            finishingSalesStop: isset($data['finishingSalesStop']) ? new Carbon($data['finishingSalesStop']) : null,
            familyStatus: FamilyStatus::tryFrom($data['familyStatus']['code'] ?? ''),
            modifiedOn: new Carbon($data['modifiedOn']),
            propertyContracts: $propertyContracts,
            characteristics: $characteristics,
            baseFinishVariant: $baseFinishVariant,
            mainArticleOrder: $mainArticleOrder,
            paidBookingContract: $paidBookingContract,
            deal: $deal,
            mortgageDemand: $mortgageDemand,
            bindedCharacteristicSales: $data['bindedCharacteristicSales'] ?? null,
            articlePrice: $data['articlePrice'] ?? null,
            sumOpportunityMinusDiscount: $data['sumOpportunityMinusDiscount'] ?? null,
            demandMainId: $data['demandMainId'] ?? null,
            depositorFizId: $data['depositorFizId'] ?? null,
            paymentPlan: $data['paymentPlan'] ?? null,
            articleId: $data['articleId'] ?? null,
            dhSerialNumber: $data['dhSerialNumber'] ?? null,
            isDigitalTransaction: $data['isDigitalTransaction'] ?? null,
            isMortgageOnlineAvailable: $isMortgageOnlineAvailable,
            depositorUrId: $data['depositorUrId'] ?? null,
            contractReservPaymentStatusCode: $data['contractReservPaymentStatus']['code'] ?? null,
        );
    }
}
