<?php

namespace App\Services\Contract;

use App\Models\Contract\Contract;
use App\Models\Contract\ContractGroup;
use App\Models\Contract\ContractService;
use App\Models\Contract\ContractStatus;
use App\Models\Contract\ContractType;
use App\Models\Sales\PaymentMode;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\Sales\ArticleOrderRepository;
use App\Services\Sales\CharacteristicSaleRepository;
use App\Services\Sales\Customer\CustomerRepository;
use App\Services\Sales\OwnerRepository;
use App\Services\Sales\PaymentPlanRepository;
use App\Services\Sales\PaymentRepository;
use Carbon\Carbon;

/**
 * Class ContractRepository
 *
 * @package App\Services\Contract
 */
class ContractRepository
{
    public function __construct(
        private readonly DynamicsCrmClient            $dynamicsCrmClient,
        private readonly PaymentRepository            $paymentRepository,
        private readonly PaymentPlanRepository        $paymentPlanRepository,
        private readonly CustomerRepository           $customerRepository,
        private readonly ArticleOrderRepository       $articleOrderRepository,
        private readonly OwnerRepository              $ownerRepository,
        private readonly CharacteristicSaleRepository $characteristicSaleRepository,
    ) {
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getById(string $id): Contract
    {
        $data = $this->dynamicsCrmClient->getContractById($id);

        return $this->makeContract($data);
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getByAccountNumber(string $accountNumber, string $customerId): ?Contract
    {
        $data = $this->dynamicsCrmClient->getContractsByType(ContractType::account(), $customerId);

        if (!isset($data['contractList'])) {
            return null;
        }

        foreach ($data['contractList'] as $contract) {
            if (isset($contract['personalAccount']) && $contract['personalAccount'] === $accountNumber) {
                return $this->makeContract($contract);
            }
        }

        return null;
    }

    /**
     * @return ?Contract[]
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getContracts(string $customerId): ?array
    {
        $data = $this->dynamicsCrmClient->getContractsByType(ContractType::account(), $customerId);

        if (!isset($data['contractList'])) {
            return null;
        }

        $contracts = [];

        foreach ($data['contractList'] as $contract) {
            $contracts[] = $this->makeContract($contract);
        }

        return $contracts;
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function getPersonalAccountByContracts(string $customerId): ?array
    {
        $data = $this->dynamicsCrmClient->getContractsByType(ContractType::account(), $customerId);

        $accountNumbers = [];
        foreach ($data['contractList'] as $contract) {
            $accountNumbers[] = $contract['personalAccount'];
        }

        return $accountNumbers;
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getDetailContracts(array $contracts, array $accounts): ?array
    {
        /** @var Contract $contract */
        $detailContracts = [];

        foreach ($contracts as $contract) {
            $detailContract = $this->dynamicsCrmClient->getDetailContracts($contract->getId())['personalAccountList'];
            $detailContracts[] = [
                'id' => $detailContract[0]['id'],
                'number' => $detailContract[0]['number'],
                'articleTypeCode' => $detailContract[0]['article']['articleTypeCode']['code'] ?? null,
                'articleVariantTm1Code' => $detailContract[0]['article']['articleVariantTm1Code']['code'] ?? null,
            ];
        }

        foreach ($accounts as $account) {
            foreach ($detailContracts as $key => $detailContract) {
                if ($detailContract['number'] === $account?->getNumber()) {
                    $detailContracts[$key]['balance'] = $account->getBalance();
                }
            }
        }

        usort($detailContracts, static function ($account1, $account2) {
            return (
                $account1['number'] < $account2['number']
            ) ? 1 : 0;
        });

        usort($detailContracts, static function ($account1, $account2) {
            return (
                $account1['balance'] < $account2['balance']
            ) ? 1 : 0;
        });

        usort($detailContracts, static function ($account1, $account2) {
            return (
                $account1['articleTypeCode'] === 8 &&
                $account1['articleVariantTm1Code'] !== 4096 &&
                $account1['balance'] >= 0
            ) ? 1 : 0;
        });

        usort($detailContracts, static function ($account1, $account2) {
            return (
                $account1['articleTypeCode'] === 4 &&
                $account1['balance'] >= 0
            ) ? 1 : 0;
        });

        usort($detailContracts, static function ($account1, $account2) {
            return (
                $account1['articleTypeCode'] === 8 &&
                $account1['articleVariantTm1Code'] === 4096 &&
                $account1['balance'] >= 0
            ) ? 1 : 0;
        });

        usort($detailContracts, static function ($account1, $account2) {
            return (
                $account1['articleTypeCode'] === 2 &&
                $account1['balance'] >= 0
            ) ? 1 : 0;
        });

        usort($detailContracts, static function ($account1, $account2) {
            return (
                $account1['articleTypeCode'] === 8 &&
                $account1['articleVariantTm1Code'] === 4096 &&
                $account1['balance'] < 0
            ) ? 1 : 0;
        });

        usort($detailContracts, static function ($account1, $account2) {
            return (
                $account1['articleTypeCode'] === 4 &&
                $account1['balance'] < 0
            ) ? 1 : 0;
        });

        usort($detailContracts, static function ($account1, $account2) {
            return (
                $account1['articleTypeCode'] === 8 &&
                $account1['articleVariantTm1Code'] !== 4096 &&
                $account1['balance'] < 0
            ) ? 1 : 0;
        });

        usort($detailContracts, function ($account1, $account2) {
            return (
                $account1['articleTypeCode'] === 2 &&
                $account1['balance'] < 0
            ) ? 1 : 0;
        });

        $detailContracts = array_reverse($detailContracts);
        $returnedAccounts = [];

        foreach ($detailContracts as $key => $val) {
            foreach ($accounts as $account) {
                if ($val['number'] === $account?->getNumber()) {
                    $returnedAccounts[$key] = $account;
                }
            }
        }

        return $returnedAccounts;
    }

    /**
     * @return ?Contract[]
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getContractsForUser(string $customerId): ?array
    {
        $data = $this->dynamicsCrmClient->getContractsByType(ContractType::account(), $customerId);

        if (!isset($data['contractList'])) {
            return null;
        }

        $contracts = [];

        foreach ($data['contractList'] as $contract) {
            if (isset($contract['personalAccount'])) {
                $contracts[] = $contract['personalAccount'];
            }
        }

        return $contracts;
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getUserAccountNumbersAndRoles(string $customerId): ?array
    {
        $data = $this->dynamicsCrmClient->getContractsByType(ContractType::account(), $customerId);

        if (!isset($data['contractList'])) {
            return null;
        }

        $contracts = [];
        $accounts = [];

        foreach ($data['contractList'] as $contract) {
            if (isset($contract['personalAccount'])) {
                $jointOwner = array_filter($contract['jointOwners'], function ($jointOwner) use ($customerId) {
                    return ($jointOwner['contactId'] ?? '') === $customerId;
                });

                $jointOwner = reset($jointOwner);

                if ($jointOwner === false) {
                    continue;
                }


                $data = [
                    'role' => $jointOwner['roleCode']['code'],
                    'accountNumber' => $contract['personalAccount']
                ];

                $accounts[] = $data;
            }
        }

        return $accounts;
    }

    /**
     * @return Contract[]
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getAllByPropertyId(string $propertyId, string $customerId): array
    {
        $data = $this->dynamicsCrmClient->getContractsByPropertyId($propertyId, $customerId);

        if (!isset($data['contracts'])) {
            return [];
        }

        return array_map(fn($data) => $this->makeContract($data), $data['contracts']);
    }

    public function makeContract(array $data): Contract
    {
        $baseFinishVariant = null;

        if (isset($data['baseFinishVariant'])) {
            $baseFinishVariant = $this->characteristicSaleRepository
                ->makeCharacteristicSale($data['baseFinishVariant']);
        }

        $payments = [];

        if (isset($data['payments'])) {
            foreach ($data['payments'] as $payment) {
                $payments[] = $this->paymentRepository->makePayment($payment);
            }
        }

        $paymentPlans = [];

        if (isset($data['paymentPlan'])) {
            foreach ($data['paymentPlan'] as $paymentPlan) {
                $paymentPlans[] = $this->paymentPlanRepository->makePaymentPlan($paymentPlan);
            }
        }

        $jointOwners = [];

        if (isset($data['jointOwners'])) {
            foreach ($data['jointOwners'] as $jointOwner) {
                if (isset($jointOwner['customerType']['code']) && $jointOwner['customerType']['code'] !== 1) {
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

        return new Contract(
            id: $data['id'],
            name: $data['name'] ?? '',
            group: isset($data['contractGroup']) ?
                ContractGroup::from($data['contractGroup']) : ContractGroup::contract(),
            date: isset($data['date']) ? new Carbon($data['contractDate']) : null,
            estimated: $data['estimated'] ?? null,
            serviceId: $data['serviceMainId'] ?? null,
            service: ContractService::tryFrom($data['serviceMain']['code'] ?? ''),
            status: ContractStatus::tryFrom($data['status']['code'] ?? ''),
            stepName: $data['stepName'] ?? null,
            debtPlanSum: $data['debtPlanSum'] ?? null,
            percentPay: $data['percentPay'] ?? null,
            registrationFilingDate: isset($data['registrationFilingDate']) ?
                new Carbon($data['registrationFilingDate']) : null,
            registrationDate: isset($data['registrationDate']) ? new Carbon($data['registrationDate']) : null,
            registrationNumber: $data['registrationNumber'] ?? null,
            paymentPlans: $paymentPlans,
            payments: $payments,
            jointOwners: $jointOwners,
            articleOrders: $articleOrders,
            creditNumber: $data['creditNumber'] ?? null,
            creditDate: isset($data['creditDate']) ? new Carbon($data['creditDate']) : null,
            owner: isset($data['ownerObject']) ? $this->ownerRepository->makeOwner($data['ownerObject']) : null,
            demandId: $data['demandId'] ?? null,
            transferDeedDate: isset($data['transferdeed']) ? new Carbon($data['transferdeed']) : null,
            registrationStage: $data['registrationStage'] ?? null,
            hypothecBankId: $data['hypothecBankId'] ?? null,
            letterOfCreditBankId: $data['letterOfCreditBankId'] ?? null,
            dateOfSigningFact: isset($data['dateOfSigningFact']) ? new Carbon($data['dateOfSigningFact']) : null,
            receiptData: isset($data['receiptData']) ? new Carbon($data['receiptData']) : null,
            modifiedOn: isset($data['modifiedOn']) ? new Carbon($data['modifiedOn']) : null,
            dateOfSigningPlan: isset($data['dateOfSigningPlan']) ? new Carbon($data['dateOfSigningPlan']) : null,
            letterOfCreditStatus: $data['letterOfCreditStatus'] ?? null,
            sumDiscount: $data['sumDiscount'] ?? null,
            paymentModeCode: PaymentMode::tryFrom($data['paymentModeCode']['code'] ?? ''),
            baseFinishVariant: $baseFinishVariant,
            personalAccount: $data['personalAccount'] ?? null,
            depositorFizId: $data['depositorFizId'] ?? null,
            depositorUrId: $data['DepositorUrId'] ?? null,
        );
    }
}
