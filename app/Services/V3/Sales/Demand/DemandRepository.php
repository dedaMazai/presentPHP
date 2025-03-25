<?php

namespace App\Services\V3\Sales\Demand;

use App\Models\Banks;
use App\Models\Contract\ContractService;
use App\Models\Project\Project;
use App\Models\Sales\Deal;
use App\Models\Sales\JointOwner\JointOwner;
use App\Models\Sales\OwnerType;
use App\Models\Sales\TradeIn;
use App\Models\V2\Sales\Demand\Demand;
use App\Models\V2\Sales\Demand\DemandBookingStatus;
use App\Models\V2\Sales\Demand\DemandBookingType;
use App\Models\V2\Sales\Demand\DemandState;
use App\Models\V2\Sales\Demand\DemandStatus;
use App\Models\V2\Sales\Demand\DemandType;
use App\Models\Sales\FamilyStatus;
use App\Models\Sales\PaymentMode;
use App\Models\User\User;
use App\Models\V2\Sales\LetterOfCreditBank;
use App\Services\Contract\ContractRepository;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\V3\Sales\StagesRepository;
use App\Services\V2\Sales\ArticleOrderRepository;
use App\Services\V2\Sales\CharacteristicSaleRepository;
use App\Services\V2\Sales\Customer\CustomerRepository;
use App\Services\Sales\Deal\DealService;
use App\Services\Sales\OwnerRepository;
use App\Services\Sales\OwnershipRepository;
use App\Services\Sales\PaymentPlanRepository;
use App\Services\V2\Sales\Property\PropertyRepository;
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
        private readonly DynamicsCrmClient            $dynamicsCrmClient,
        private readonly PropertyRepository           $propertyRepository,
        private readonly ContractRepository           $contractRepository,
        private readonly PaymentPlanRepository        $paymentPlanRepository,
        private readonly CustomerRepository           $customerRepository,
        private readonly ArticleOrderRepository       $articleOrderRepository,
        private readonly OwnershipRepository          $ownershipRepository,
        private readonly StagesRepository             $stagesRepository,
        private readonly OwnerRepository              $ownerRepository,
        private readonly CharacteristicSaleRepository $characteristicSaleRepository,
        private readonly DealService                  $dealService,
    ) {
    }

    /**
     * @return Demand[]
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getDemands(User $user, ?string $parentId = ''): array
    {
        $data = $this->dynamicsCrmClient->getDemandsV2($user);
        $demands = [];

        foreach ($data['demandList'] as $demandData) {
            if (!DemandType::tryFrom($demandData['demandType']['code'])) {
                continue;
            }

            $demands[] = $this->makeDemand($demandData, $user);
        }

        return $demands;
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

        if (!isset($data['demandSubType'])) {
            $data['demandSubType']['code'] = null;
        }

        if (isset($data['articleId'])) {
            $property = $this->propertyRepository->getById($data['articleId']);
            $propertyContracts = $this->contractRepository->getAllByPropertyId($data['articleId'], $user->crm_id);

            foreach ($propertyContracts as $propertyContract) {
                if ($propertyContract->getService() &&
                    ContractService::paidBooking()->equals($propertyContract->getService())
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
            if ($data['customerType'] == 2) {
                $customer = $this->customerRepository->getById($data['customerId']);
            } else {
                $customer = $this->customerRepository->createEmptyCustomer();
            }

            foreach ($data['jointOwners'] as $jointOwner) {
                if ((($jointOwner["contactId"]??'') === $data['customerId']) ||
                    (($jointOwner["accountId"]??'') === $data['customerId'])) {
                    $customer->setCustomerType($jointOwner["customerType"]);
                    break;
                }
            }
        }

        $baseFinishVariant = null;

        if (isset($data['baseFinishVariant'])) {
            $baseFinishVariant = $this->characteristicSaleRepository->makeCharacteristicSale($data['baseFinishVariant']);
        }

        $paymentPlans = [];
        if (isset($data['paymentPlans'])) {
            foreach ($data['paymentPlans'] as $paymentPlan) {
                $paymentPlans[] = $this->paymentPlanRepository->makePaymentPlan($paymentPlan);
            }
        }

        $jointOwners = [];

        if (isset($data['depositorFizId'])) {
            foreach ($data['jointOwners'] as $key => $jointOwner) {
                if (($jointOwner['contactId'] ?? null) === $data['depositorFizId']) {
                    $data['jointOwners'][$key]["isDepositor"] = true;
                } else {
                    $data['jointOwners'][$key]["isDepositor"] = false;
                }
            }
        }

        foreach ($data['jointOwners'] as $key => $jointOwner) {
            if (($jointOwner['contactId'] ?? null) === $user->crm_id) {
                $data['jointOwners'][$key]["isCustomer"] = true;
            } else {
                $data['jointOwners'][$key]["isCustomer"] = false;
            }
        }

        // phpcs:disable
        $personalOwnerships = collect($data['jointOwners'])->where('ownerType.code', '=', OwnerType::personal()->value);
        $sharedOwnerships = collect($data['jointOwners'])->where('ownerType.code', '=', OwnerType::shared()->value);
        $jointOwnerships = collect($data['jointOwners'])->where('ownerType.code', '=', OwnerType::joint()->value);
        $ownership = null;

        if ($personalOwnerships->count() !== 0) {
            $ownership = $this->ownershipRepository->makeOwnership([
                'code' => OwnerType::personal()->value,
                'name' => OwnerType::personal()->label,
                'message' => '',
            ]);
        } elseif ($sharedOwnerships->count() !== 0) {
            $ownership = $this->ownershipRepository->makeOwnership([
                'code' => OwnerType::shared()->value,
                'name' => OwnerType::shared()->label,
                'message' => '',
            ]);
        } elseif ($jointOwnerships->count() !== 0) {
            $ownership = $this->ownershipRepository->makeOwnership([
                'code' => OwnerType::joint()->value,
                'name' => OwnerType::joint()->label,
                'message' => '',
            ]);
        }

        $jointOwnersCount = count($data['jointOwners']);

        if ($jointOwnersCount === 1 && $ownership?->getCode() === 5) {
            $data['jointOwners'][0]['label'] = 'Участник N1';
        } elseif ($jointOwnersCount === 2 && $ownership?->getCode() === 5) {
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
            } elseif (!$sIsCustomer) {
                if (Carbon::parse($data['jointOwners'][1]['birthDate'])->age >= 18) {
                    $data['jointOwners'][1]['label'] = 'Участник N1';
                    $data['jointOwners'][0]['label'] = 'Доверенное лицо';
                }

                if (Carbon::parse($data['jointOwners'][1]['birthDate'])->age < 18) {
                    $data['jointOwners'][1]['label'] = 'Участник N1';
                    $data['jointOwners'][0]['label'] = 'Представитель ребенка';
                }
            }
        } elseif ($jointOwnersCount === 2 && $ownership?->getCode() === 1) {
            if ($data['jointOwners'][0]['isCustomer'] ?? null === true) {
                $data['jointOwners'][0]['label'] = 'Участник N1';
                $data['jointOwners'][1]['label'] = 'Участник N2';
            } elseif ($data['jointOwners'][1]['isCustomer'] ?? null === true) {
                $data['jointOwners'][1]['label'] = 'Участник N1';
                $data['jointOwners'][0]['label'] = 'Участник N2';
            }
        } elseif ($jointOwnersCount >= 2 && $ownership?->getCode() === 2) {
            $ownershipForms = [];
            $indexOwner = 1;

            foreach ($data['jointOwners'] as $index => $joinOwner) {
                if (($joinOwner['isCustomer'] ?? false) && ($joinOwner['ownerType']['code'] ?? 0) === 4) {
                    $ownershipForms[$index] = 'Представитель/Доверенное лицо';
                } elseif (($joinOwner['isCustomer'] ?? false) && ($joinOwner['ownerType']['code'] ?? 0) === 2) {
                    $ownershipForms[$index] = 'Участник N1';
                    ++$indexOwner;
                } else {
                    $ownershipForms[$index] = 'Участник N' . $indexOwner;
                    ++$indexOwner;
                }
            }

            foreach ($data['jointOwners'] as $key => $joinOwner) {
                $data['jointOwners'][$key]['label'] = $ownershipForms[$key];
            }
        }
        // phpcs:enable

        if (isset($data['jointOwners'])) {
            foreach ($data['jointOwners'] as $jointOwn) {
                if ($jointOwn['roleCode']['code'] == 1) {
                    $jointOwners[] = $this->customerRepository->makeCustomer($jointOwn);
                } elseif ($jointOwn['roleCode']['code'] == 8) {
                    $borrowers[] = $this->customerRepository->makeCustomer($jointOwn);
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

                if (!$mainArticleOrder && $property && $articleOrder->getPropertyId() === $property->getId()) {
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

        // phpcs:disable
        $tradeIn = new TradeIn(
            $isTradeInAvailable = true,
            $tradeInInfo = 'Приобрести недвижимость по программе trade-in можно в жилых кварталах LIFE-Ботанический сад, LIFE-Кутузовский, LIFE-Варшавская, а также в апарт-отелях YE’S Ботанический сад и YE’S Технопарк',
        );

        if (isset($data['articleOrders'])) {
            $article = collect($data['articleOrders'])->firstWhere('id', 8);

            if ((($data['demandSubType']['code'] ?? null) === DemandBookingType::tradeIn()->value ||
                    isset($article['serviceCode'])) && ($article['serviceCode'] ?? null) === '020020') {
                $tradeIn = new TradeIn(
                    $isTradeInAvailable = false,
                    $tradeInInfo = '',
                );
            }
        }
        // phpcs:disable

        if (DemandStatus::reservation()->value === ($data['status']['code'])) {
            $isBookingCancelAvailable = true;
        } else {
            $isBookingCancelAvailable = false;
        }

        $isMortgageAvailable = true;

        if (isset($data['articleOrders'])) {
            if (collect($data['articleOrders'])->firstWhere('serviceCode', '=', '020020')) {
                $isMortgageAvailable = false;
            }
        }

        // phpcs:disable
        if ($data['demandSubType']['code'] === 1 ||
            (($data['demandSubType']['code'] == 2 ||
                    $data['demandSubType']['code'] == 8 ||
                    $data['demandSubType']['code'] == 16) &&
                (($data['reserveOpportunityName'] ?? null) != null &&
                    ($data['contractReservPaymentStatus']['code'] ?? null) == 1))) {
            $isMortgageOnlineAvailable = false;
            $mortgageOnlineInfo = 'Для подачи заявки на одобрение кредита, оплатите бронь. Стоимость бронирования вычитается из общей стоимости объекта при оформлении сделки. Если вы не получите одобрение кредита, стоимость бронирования будет возвращена.';
        } else {
            $isMortgageOnlineAvailable = true;
            $mortgageOnlineInfo = 'Вы можете подать заявку на получение одобрения кредита в банках-партнерах. Если у вас уже есть предварительное одобрение кредита, выберите соответствующий пункт меню и укажите информацию.';
        }

        $isEarlyPreviouslyOwners = JointOwner::where('user_id', '=', $user->id)->count() > 0;

        $isAvailableChangePaymentMode = true;
        if ((($data['bindedCharacteristicSales']??null) === null &&
                ($data['articlePrice'] !== $data['sumOpportunityMinusDiscount'])) ||
            (($data['bindedCharacteristicSales']??null) === null && count($data['paymentPlan']) > 1)) {
            $isAvailableChangePaymentMode = false;
        }

        if ($isAvailableChangePaymentMode === true) {
            foreach ($data['bindedCharacteristicSales'] ?? [] as $bindedCharacteristicSale) {
                if ($bindedCharacteristicSale['processingType']['code'] === 16 &&
                    ($data['articlePrice'] !== $data['sumOpportunityMinusDiscount'])) {
                    $isAvailableChangePaymentMode = false;
                } else {
                    $isAvailableChangePaymentMode = true;
                    break;
                }
            }
        }
        // phpcs:enable

        if ($isAvailableChangePaymentMode === true) {
            foreach ($data['bindedCharacteristicSales'] ?? [] as $bindedCharacteristicSale) {
                if ($bindedCharacteristicSale['processingType']['code'] === 8 && count($data['paymentPlan']) > 1) {
                    $isAvailableChangePaymentMode = false;
                } else {
                    $isAvailableChangePaymentMode = true;
                    break;
                }
            }
        }

        $instalmentsInfo = null;
        $isAvailibleInstalments = false;
        $instalments = [];

        foreach ($characteristics as $characteristic) {
            if ($characteristic->getType()->value === 16) {
                if ($characteristic->getChoiceSet()) {
                    $isAvailibleInstalments = true;
                } elseif (count($data['paymentPlan']) > 1) {
                    $isAvailibleInstalments = false;
                    // phpcs:disable
                    $instalmentsInfo = 'Для объекта определены персональные условия рассрочки. В случае возникновения вопросов, пожалуйста, свяжитесь с менеджером';
                    // phpcs:enable
                } elseif (count($data['paymentPlan']) === 1) {
                    $isAvailibleInstalments = true;
                    $instalmentsInfo = null;
                }

                $instalments[] = [
                    'id' => $characteristic->getId(),
                    'name' => $characteristic->getName(),
                    'is_selected' => $characteristic->getChoiceSet()??false
                ];
            }
        }

        // phpcs:disable
        $paymentBooking = null;

        if ($data['demandSubType']['code'] == 1) {
            $paymentBooking['booking_payment_end_at'] = Carbon::parse($data['endDate'] ?? null)->addHours(3)->format('Y-m-d H:i:s');
            $project = Project::whereJsonContains('booking_property', [['crm_id' => $property->getAddress()->getId()]])->first();
            $cost = 15000;

            if ($project !== null) {
                foreach ($project->booking_property as $item) {
                    if ($item["crm_id"] === $property->getAddress()->getId()) {
                        $cost = $item['paid_booking_cost'];
                        break;
                    }
                }
            }

            $crmId = $property->getAddress()->getId();

            $kes = null;

            if ($project?->booking_property) {
                $kes = array_filter($project->booking_property, static function($subArray) use ($crmId) {
                    return $subArray['crm_id'] !== $crmId;
                });
            }

            $needed = 7;

            if ($kes != null) {
                $needed = array_shift($kes)['paid_booking_expiry_time'];
            }

            $paymentBooking['paid_booking_cost'] = $cost;
            $paymentBooking['paid_booking_expiry_time'] = $needed;
            $paymentBooking['booking_payment_time'] = null;
        } elseif ($data['demandSubType']['code'] == 2 ||
            $data['demandSubType']['code'] == 8 ||
            ($data['demandSubType']['code'] == 16 && ($data['reserveOpportunityName'] ?? null) != null &&
            ($data['contractReservPaymentStatus']['code'] ?? null) == 1)) {
            $paymentBooking['paid_booking_cost'] = null;

            if ($data['demandSubType']['code'] == 2 || $data['demandSubType']['code'] == 16) {
                $project = Project::whereJsonContains('booking_property', [['crm_id' => $property->getAddress()->getId()]])->first();
                $cost = 15000;

                if ($project !== null) {
                    foreach ($project->booking_property as $item) {
                        if ($item["crm_id"] == $property->getAddress()->getId()) {
                            $cost = $item['paid_booking_cost'];
                            break;
                        }
                    }
                }

                $paymentBooking['paid_booking_cost'] = $cost;
            } elseif ($data['demandSubType']['code'] == 8) {
                $contractsByProperty = $this->dynamicsCrmClient->getContractsByPropertyId($user->crm_id, $articleOrders[0]?->getId());

                $contractsByProperty = collect($contractsByProperty['contracts'])->filter(function ($contract) {
                    return $contract['serviceMain']['code'] == '030043';
                });
                if ($contractsByProperty != null) {
                    $paymentBooking['paid_booking_cost'] = $contractsByProperty[0]['estimated'];
                }
            }
            $paymentBooking['booking_payment_end_at'] = Carbon::parse($data['endDate']??null)->addHours(3)->format('Y-m-d H:i:s');
            $paymentBooking['paid_booking_expiry_time'] = null;
            $paymentBooking['booking_payment_time'] = null;
        } elseif ($data['demandSubType']['code'] == 1 ||
            ($data['demandSubType']['code'] == 2 ||
                $data['demandSubType']['code'] == 8 ||
                ($data['demandSubType']['code'] == 16 &&
                    ($data['reserveOpportunityName'] ?? null) != null &&
                    ($data['contractReservPaymentStatus']['code'] ?? null) == 1))) {
            $paymentBooking['booking_payment_end_at'] = Carbon::parse($data['endDate']??null)->addHours(3)->format('Y-m-d H:i:s');
            $paymentBooking['paid_booking_cost'] = null;
            $paymentBooking['paid_booking_expiry_time'] = null;
            $paymentBooking['booking_payment_time'] = null;
        } elseif ($data['demandSubType']['code'] == 1 ||
            (($data['demandSubType']['code'] == 2 ||
                    $data['demandSubType']['code'] == 8 ||
                    ($data['demandSubType']['code'] == 16 &&
                        $data['reserveOpportunityName'] != null &&
                        ($data['contractReservPaymentStatus']['code'] ?? null) == 1)) &&
                $data['stepName'] != 'Бронирование')) {
            $paymentBooking['booking_payment_end_at'] = null;
            $paymentBooking['paid_booking_cost'] = null;
            $paymentBooking['paid_booking_expiry_time'] = null;
            $paymentBooking['booking_payment_time'] = null;
        }
        // phpcs:enable

        $isPaymentBookingAvailible = false;

        if ($data['demandSubType']['code'] == 1 ||
            (($data['demandSubType']['code'] == 2 ||
                    $data['demandSubType']['code'] == 8 ||
                    $data['demandSubType']['code'] == 16) &&
                ($data['reserveOpportunityName'] ?? null) != null &&
                $data['contractReservPaymentStatus']['code'] == 1)) {
            $isPaymentBookingAvailible = true;
        }

        if (!$isPaymentBookingAvailible) {
            // phpcs:disable
            $changePaymentModeDisableInfo = 'Для Вас определены персональные условия оплаты. Для получения дополнительной информации свяжитесь с менеджером';
            // phpcs:enable
        } else {
            $changePaymentModeDisableInfo = null;
        }

        $letterOfCreditBankId = $data['letterOfCreditBankId'] ?? null;
        $propertyArt = $this->dynamicsCrmClient->getPropertyById($data['articleId']);
        $escrowBankId = $propertyArt['escrowBankId'] ?? null;
        $user = $this->dynamicsCrmClient->getCustomerById($user->crm_id);
        $isSberClient = $user['isSberClient'];

        $nextStepsBanksInfo = '';
        $sberBankId = '5f399d29-60b9-40f6-8d1a-935fbfa98977';
        $wtbBankId = '4616b662-1606-459d-b23b-fbf539d51ef9';
        $otherBankId = '0af0ffcb-060d-ea11-942d-005056bf3b92';
        $domrfBankId = 'd417af4e-bc76-e711-9402-005056bf3b92';
        $alfaBankId = '5d5c6d4f-bb72-e711-9402-005056bf3b92';

        if ($escrowBankId && $letterOfCreditBankId == null) {
            $nextStepsBanksInfo = null;
        } elseif ($escrowBankId === null && $letterOfCreditBankId === $sberBankId && $isSberClient === true) {
            $nextStepsBanksInfo = config('bank_info_message.instruction_for_opening_from_manager');
        } elseif ($escrowBankId === null && $letterOfCreditBankId === $sberBankId && $isSberClient === false) {
            $nextStepsBanksInfo = config('bank_info_message.contact_the_sber');
        } elseif ($escrowBankId === null && $letterOfCreditBankId === $wtbBankId) {
            $nextStepsBanksInfo = config('bank_info_message.two_payment_receipts');
        } elseif ($escrowBankId === null && $letterOfCreditBankId === $otherBankId) {
            $nextStepsBanksInfo = config('bank_info_message.contact_the_manager');
        } elseif ($escrowBankId === $sberBankId && $letterOfCreditBankId === $wtbBankId) {
            $nextStepsBanksInfo = config('bank_info_message.sber_escrow_documents');
        } elseif ($escrowBankId === $sberBankId && $letterOfCreditBankId === $sberBankId && $isSberClient === true) {
            $nextStepsBanksInfo = config('bank_info_message.managers_prepare_the_documents');
        } elseif ($escrowBankId === $sberBankId && $letterOfCreditBankId === $sberBankId && $isSberClient === false) {
            $nextStepsBanksInfo = config('bank_info_message.sber_escrow');
        } elseif ($escrowBankId === $sberBankId && $letterOfCreditBankId === $otherBankId) {
            $nextStepsBanksInfo = config('bank_info_message.contact_the_manager');
        } elseif ($escrowBankId === $domrfBankId && $letterOfCreditBankId === $wtbBankId) {
            $nextStepsBanksInfo = config('bank_info_message.domrf_two_receipts');
        } elseif ($escrowBankId === $domrfBankId && $letterOfCreditBankId === $sberBankId && $isSberClient === true) {
            $nextStepsBanksInfo = config('bank_info_message.domrf_with_instruction');
        } elseif ($escrowBankId === $domrfBankId && $letterOfCreditBankId === $sberBankId && $isSberClient === false) {
            $nextStepsBanksInfo = config('bank_info_message.domrf_ofice');
        } elseif ($escrowBankId === $domrfBankId && $letterOfCreditBankId === $otherBankId) {
            $nextStepsBanksInfo = config('bank_info_message.contact_the_manager');
        } elseif ($escrowBankId === $alfaBankId && $letterOfCreditBankId === $alfaBankId) {
            $nextStepsBanksInfo = config('bank_info_message.alfa_requisites');
        } elseif ($escrowBankId === $alfaBankId && $letterOfCreditBankId === $wtbBankId) {
            $nextStepsBanksInfo = config('bank_info_message.alfa_two_receipts');
        } elseif ($escrowBankId === $alfaBankId && $letterOfCreditBankId === $sberBankId && $isSberClient === true) {
            $nextStepsBanksInfo = config('bank_info_message.alfa_managers');
        } elseif ($escrowBankId === $alfaBankId && $letterOfCreditBankId === $sberBankId && $isSberClient === false) {
            $nextStepsBanksInfo = config('bank_info_message.alfa_ofice');
        } elseif ($escrowBankId === $alfaBankId && $letterOfCreditBankId === $otherBankId) {
            $nextStepsBanksInfo = config('bank_info_message.contact_the_manager');
        }

        if ($data['isElectronicRegistration']??null === true && $data['isdigitaltransaction'] ?? null === false) {
            $isBorrowersAvailible = true;
        } else {
            $isBorrowersAvailible = false;
        }

        $letterOfCreditBank = null;

        if ($data['letterOfCreditBankId']??null) {
            $letterOfCreditBankName = Banks::where('bank_id', '=', $data['letterOfCreditBankId'])->first()?->name;
            $letterOfCreditBank = new LetterOfCreditBank(
                id: $data['letterOfCreditBankId'],
                name: $letterOfCreditBankName,
                image: ''
            );
        }

        $stages = $this->stagesRepository->makeStages($data, $characteristics, $property);
        $isTransactionTermsFull = true;

        foreach ($data['stages']??[] as $stage) {
            foreach ($stage['substage'] as $substage) {
                if ($substage['status_message'] === "Необходимо указать") {
                    $isTransactionTermsFull = false;
                    break;
                }
            }
        }

        $transactionTermsFullInfo = "";

        if ($isTransactionTermsFull) {
            // phpcs:disable
            $transactionTermsFullInfo = 'Убедитесь, что все данные по условиям сделки указаны корректно. После запроса на оформление договора с вами свяжется менеджер для дополнительного подтверждения указанных вами данных';
            // phpcs:enable
        }

        $salesScheme = null;
        $articleCodes = ['020020', '020030', '020050', '020011', '020080', '020040', '020010'];

        foreach ($articleOrders as $articleOrder) {
            if (in_array($articleOrder->getCode(), $articleCodes)) {
                $salesScheme = $articleOrder;
            }
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
            paymentPlan: $paymentPlan ?? null,
            articleId: $article['id'] ?? null,
            nextStepBankInfo: $nextStepsBanksInfo,
            isDigitalTransaction: $data['isdigitaltransaction'] ?? null,
            ownership: $ownership ?? null,
            tradeIn: $tradeIn,
            isBookingCancelAvailable: $isBookingCancelAvailable,
            isMortgageAvailable: $isMortgageAvailable,
            isMortgageOnlineAvailable: $isMortgageOnlineAvailable,
            mortgageOnlineInfo: $mortgageOnlineInfo,
            isEarlyPreviouslyOwners: $isEarlyPreviouslyOwners,
            isAvailableChangePaymentMode: $isAvailableChangePaymentMode,
            changePaymentModeDisableInfo: $changePaymentModeDisableInfo,
            instalmentsInfo: $instalmentsInfo,
            isAvailibleInstalments: $isAvailibleInstalments,
            isPaymentBookingAvailible: $isPaymentBookingAvailible,
            nextStepsBanksInfo: $nextStepsBanksInfo,
            borrowers: $borrowers ?? null,
            isBorrowersAvailible: $isBorrowersAvailible,
            paymentBooking: $paymentBooking,
            instalments: $instalments,
            letterOfCreditBank: $letterOfCreditBank,
            isTransactionTermsFull: $isTransactionTermsFull,
            transactionTermsFullInfo: $transactionTermsFullInfo,
            stages: $stages,
            salesScheme: $salesScheme
        );
    }
}
