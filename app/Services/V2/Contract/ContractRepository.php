<?php

namespace App\Services\V2\Contract;

use App\Models\Sales\OwnerType;
use App\Models\User\User;
use App\Models\V2\Contract\Contract;
use App\Models\V2\Contract\ContractGroup;
use App\Models\V2\Contract\ContractService;
use App\Models\V2\Contract\ContractStatus;
use App\Models\Contract\ContractType;
use App\Models\Sales\PaymentMode;
use App\Services\Document\DocumentRepository;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\V2\Sales\ArticleOrderRepository;
use App\Services\Sales\CharacteristicSaleRepository;
use App\Services\V2\Sales\Customer\CustomerContractConfidantRepository;
use App\Services\V2\Sales\Customer\CustomerRepository;
use App\Services\Sales\OwnerRepository;
use App\Services\Sales\OwnershipRepository;
use App\Services\Sales\PaymentPlanRepository;
use App\Services\Sales\PaymentRepository;
use App\Services\Sales\StagesRepository;
use App\Services\V2\Sales\Property\PropertyRepository;
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
        private readonly OwnershipRepository          $ownershipRepository,
        private readonly PropertyRepository           $propertyRepository,
        private readonly DocumentRepository           $documentRepository,
        private readonly StagesRepository             $stagesRepository,
        private readonly CharacteristicSaleRepository $characteristicSaleRepository,
        private readonly CustomerContractConfidantRepository $customerContractConfidant
    ) {
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getById(string $id): Contract
    {
        $data = $this->dynamicsCrmClient->getContractById($id);

        if ($data['id'] === null) {
            throw new NotFoundException("Contract not found");
        }

        return $this->makeContract($data);
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getByIdV2(string $id, User $user): Contract
    {
        $data = $this->dynamicsCrmClient->getContractById($id);

        if ($data['id'] === null) {
            throw new NotFoundException("Contract not found");
        }

        return $this->makeContractV2($data, $user);
    }

    /**
     * @return ?Contract[]
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getContracts(string $customerId): ?array
    {
        $data = $this->dynamicsCrmClient->getContractsByTypesV2($customerId);

        if (!isset($data['contractList'])) {
            return null;
        }

        $contracts = [];
        $serviceMainCodes = [
            '020020', '020030', '020050', '020011', '020080', '020040', '020010'
        ];

        // phpcs:disable
        foreach ($data['contractList'] as $contract) {
            if (($contract['estimated'] ?? null) === 0.0 ||
                isset($contract['transferdeed']) ||
                !in_array($contract['serviceMain']['code'], $serviceMainCodes)) {
                continue;
            }

            $agreementCount = 0;

            $types = "65536, 100000001, 2097152";
            $additionalAgreements = $this->dynamicsCrmClient->getContractsByTypes($types, $customerId);

            if (!empty($additionalAgreements)) {
                foreach ($additionalAgreements as $additionalAgreement) {
                    if (
                        !empty($additionalAgreement["opportunityMainId"]) &&
                        $additionalAgreement["opportunityMainId"] === $contract["id"]
                    ) {
                        $agreementCount++;
                    }
                }
            }

            $contract["contractsCount"] = $agreementCount;

            $contracts[] = $this->makeContract($contract);
        }
        // phpcs:enable

        return $contracts;
    }

        /**
     * @return ?Contract[]
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getDemandContracts(string $customerId): ?array
    {
        $data = $this->dynamicsCrmClient->getContractsByTypesV2($customerId);

        if (!isset($data['contractList'])) {
            return null;
        }

        $contracts = [];
        $serviceMainCodes = [
            '020020', '020030', '020050', '020011', '020080', '020040', '020010'
        ];

        $types = "65536, 100000001, 2097152";
        $additionalAgreements = $this->dynamicsCrmClient->getContractsByTypes($types, $customerId);

        // phpcs:disable
        foreach ($data['contractList'] as $contract) {
            if (($contract['estimated'] ?? null) === 0.0 ||
                isset($contract['transferdeed']) ||
                !in_array($contract['serviceMain']['code'], $serviceMainCodes)) {
                continue;
            }

            $agreementCount = 0;

            if (!empty($additionalAgreements['contractList'])) {
                foreach ($additionalAgreements['contractList'] as $additionalAgreement) {
                    if (
                        !empty($additionalAgreement["opportunityMainId"]) &&
                        $additionalAgreement["opportunityMainId"] === $contract["id"]
                    ) {
                        $agreementCount++;
                    }
                }
            }

            $contract["contractsCount"] = $agreementCount;

            $contracts[] = $this->makeContractForDemand($contract);
        }
        // phpcs:enable

        return $contracts;
    }

        /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function makeContractForDemand(array $data): Contract
    {
        // phpcs:disable
        $jointOwners = [];

        if (isset($data['jointOwners'])) {
            foreach ($data['jointOwners'] as $jointOwner) {
                if (isset($jointOwner['roleCode']['code']) && $jointOwner['roleCode']['code'] === 1 &&
                    ($jointOwner['customerType']['code'] ?? null) != 1) {
                    $jointOwners[] = $this->customerRepository->makeCustomer($jointOwner);
                } elseif (($jointOwner['customerType']['code'] ?? null) == 1) {
                    $emptyJointOwner = $this->customerRepository->createEmptyCustomer();
                    $emptyJointOwner->setCustomerType($data['customerType']);
                    $jointOwners[] = $emptyJointOwner;
                }
            }
        }
        // phpcs:enable

        $property = null;

        if (isset($data['articleOrders'][0])) {
            $property = $this->propertyRepository->getById($data['articleOrders'][0]['articleId']);
        }

        $ownership = null;

        if (isset($data['jointOwners'])) {
            // phpcs:disable
            $personalOwnerships = collect($data['jointOwners'])->where('ownerType.code', '=', OwnerType::personal()->value);
            $sharedOwnerships = collect($data['jointOwners'])->where('ownerType.code', '=', OwnerType::shared()->value);
            $jointOwnerships = collect($data['jointOwners'])->where('ownerType.code', '=', OwnerType::joint()->value);
            // phpcs:enable

            if ($personalOwnerships->count() !== 0) {
                $ownership = $this->ownershipRepository->makeOwnership([
                    'code' => OwnerType::personal()->value,
                    'name' => OwnerType::personal()->label,
                ]);
            } elseif ($sharedOwnerships->count() !== 0) {
                $ownership = $this->ownershipRepository->makeOwnership([
                    'code' => OwnerType::shared()->value,
                    'name' => OwnerType::shared()->label,
                ]);
            } elseif ($jointOwnerships->count() !== 0) {
                $ownership = $this->ownershipRepository->makeOwnership([
                    'code' => OwnerType::joint()->value,
                    'name' => OwnerType::joint()->label,
                ]);
            }
        }

        $articleOrders = [];

        if (isset($data['articleOrders'])) {
            foreach ($data['articleOrders'] as $articleOrder) {
                $articleOrders[] = $this->articleOrderRepository->makeArticleOrder($articleOrder);
            }
        }

        $salesScheme = null;
        $articleCodes = ['020020', '020030', '020050', '020011', '020080', '020040', '020010'];

        foreach ($articleOrders as $articleOrder) {
            if (in_array($articleOrder->getCode(), $articleCodes)) {
                $salesScheme = $articleOrder;
            }
        }

        $contractInfo = $this->getContractInfo($data);

        if (($data['paymentModeCode']['code'] ?? null) === 4 &&
            $data['stepName'] === 'Регистрация') {
        }

        return new Contract(
            id: $data['id'],
            name: $data['name'],
            group: null,
            date: null,
            estimated: $data['estimated'] ?? null,
            estimatedWoBTI: null,
            serviceId: null,
            service: ContractService::tryFrom($data['serviceMain']['code'] ?? ''),
            status: null,
            stepName: $data['stepName'] ?? null,
            debtPlanSum: null,
            percentPay: null,
            percentPayWoBTI: null,
            registrationFilingDate: null,
            registrationDate: null,
            paymentPlans: null,
            payments: null,
            jointOwners: $jointOwners ?? null,
            articleOrders: $articleOrders,
            creditNumber: null,
            creditDate: null,
            owner: null,
            demandId: null,
            transferDeedDate: null,
            registrationStage: null,
            hypothecBankId: null,
            letterOfCreditBankId: null,
            dateOfSigningFact: null,
            receiptData: null,
            modifiedOn: isset($data['modifiedOn']) ? new Carbon($data['modifiedOn']) : null,
            dateOfSigningPlan: null,
            letterOfCreditStatus: null,
            sumDiscount: null,
            paymentModeCode: null,
            baseFinishVariant: null,
            personalAccount: null,
            contractInfo: $contractInfo,
            ownership: null,
            salesScheme: null,
            isContractApprove: null,
            property: $property,
            isSignAppAvailible: '',
            electroReg: null,
            electroRegInfo: null,
            stages: null,
            isRequiredSmsCode: null,
            smsCodeType: null,
            draftContractDocument: null,
            draftContractDocumentInfo: null,
            delayDays: null,
            registrationNumber: null,
            bankDate: null,
            branchAddressHb: null,
            bankManagerEmail: null,
            bankManagerMobilePhone: null,
            bankManagerFullName: null,
            isHypothecSup: null,
            opportunityMainId: null,
            esValidityDate: null,
            isDigitalTransaction: null,
            contractsCount: $data['contractsCount'] ?? null,
        );
        // phpcs:enable
    }


    /**
     * @return ?Contract[]
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getArchiveContracts(string $customerId): ?array
    {
        $data = $this->dynamicsCrmClient->getContractsByTypesV2($customerId);

        return $data['contractList'] ?? null;
//        $contracts = [];
//
//        // phpcs:disable
//        foreach ($data['contractList'] as $contract) {
//            $contracts[] = $this->makeContract($contract);
//        }
//        // phpcs:enable
//
//        return $contracts;
    }

    /**
     * @return ?Contract[]
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getContractsByTypes(string $types, string $customerId): ?array
    {
        $data = $this->dynamicsCrmClient->getContractsByTypes($types, $customerId);

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
     * @throws BadRequestException
     * @throws NotFoundException
     */
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

        // phpcs:disable
        $jointOwners = [];

        if (isset($data['jointOwners'])) {
            foreach ($data['jointOwners'] as $jointOwner) {
                if (isset($jointOwner['roleCode']['code']) && $jointOwner['roleCode']['code'] === 1 &&
                    ($jointOwner['customerType']['code'] ?? null) != 1) {
                    $jointOwners[] = $this->customerRepository->makeCustomer($jointOwner);
                } elseif (($jointOwner['customerType']['code'] ?? null) == 1) {
                    $emptyJointOwner = $this->customerRepository->createEmptyCustomer();
                    $emptyJointOwner->setCustomerType($data['customerType']);
                    $jointOwners[] = $emptyJointOwner;
                }
            }
        }
        // phpcs:enable

        $property = null;

        if (isset($data['articleOrders'][0])) {
            $property = $this->propertyRepository->getById($data['articleOrders'][0]['articleId']);
        }

        $ownership = null;

        if (isset($data['jointOwners'])) {
            // phpcs:disable
            $personalOwnerships = collect($data['jointOwners'])->where('ownerType.code', '=', OwnerType::personal()->value);
            $sharedOwnerships = collect($data['jointOwners'])->where('ownerType.code', '=', OwnerType::shared()->value);
            $jointOwnerships = collect($data['jointOwners'])->where('ownerType.code', '=', OwnerType::joint()->value);
            // phpcs:enable

            if ($personalOwnerships->count() !== 0) {
                $ownership = $this->ownershipRepository->makeOwnership([
                    'code' => OwnerType::personal()->value,
                    'name' => OwnerType::personal()->label,
                ]);
            } elseif ($sharedOwnerships->count() !== 0) {
                $ownership = $this->ownershipRepository->makeOwnership([
                    'code' => OwnerType::shared()->value,
                    'name' => OwnerType::shared()->label,
                ]);
            } elseif ($jointOwnerships->count() !== 0) {
                $ownership = $this->ownershipRepository->makeOwnership([
                    'code' => OwnerType::joint()->value,
                    'name' => OwnerType::joint()->label,
                ]);
            }
        }

        $articleOrders = [];

        if (isset($data['articleOrders'])) {
            foreach ($data['articleOrders'] as $articleOrder) {
                $articleOrders[] = $this->articleOrderRepository->makeArticleOrder($articleOrder);
            }
        }

        $salesScheme = null;
        $articleCodes = ['020020', '020030', '020050', '020011', '020080', '020040', '020010'];

        foreach ($articleOrders as $articleOrder) {
            if (in_array($articleOrder->getCode(), $articleCodes)) {
                $salesScheme = $articleOrder;
            }
        }

        $contractInfo = $this->getContractInfo($data);

        // phpcs:disable
        $electroReg = 'no';
        $electroRegInfo = null;

        if (isset($data['electroReg'])) {
            if ($data['electroReg'] === true) {
                $electroReg = 'yes';
            } elseif ($data['electroReg'] === false) {
                $electroRegInfo = 'Сделка проходит не в электронном формате. Подписание договора будет проходить в офисе Пионера';
            }
        }

        if (($data['hypothecBankId'] ?? null) === '5f399d29-60b9-40f6-8d1a-935fbfa98977') {
            $electroReg = 'sber';
            $electroRegInfo = 'Формированием электронной подписи занимается менеджер банка. Подписание договора будет проходить через платформу электронной регистрации Сбербанка — ДомКлик посредством 4-значного кода из смс сообщения, которое направит менеджер Пионера';
        } elseif (($data['hypothecBankId'] ?? null) === '3e45c241-ea79-ed11-bbad-005056bfae62') {
            $electroReg = 'tinkoff';
            $electroRegInfo = 'Формированием электронной подписи занимается менеджер банка. Подписание договора будет проходить в мобильном приложении Тинькофф';
        }

        $isDocumentApprove = null;
        $documentsApprove = $this->documentRepository->getContractDocumentsWithTypeCode($data['id'], [128,1024,2048]);

        if (count($documentsApprove) !== 0) {
            $fk = array_key_first($documentsApprove);
            $isDocumentApprove = $documentsApprove[$fk]->getIsDocumentApprove();
        }

        $stages = $this->stagesRepository->makeContractStages(demand: $data, isContractApprove: $isDocumentApprove);

        $isRequiredSmsCode = false;

        if (($data['paymentModeCode']['code'] ?? null) === 4 &&
            $data['hypothecBankId'] === '5f399d29-60b9-40f6-8d1a-935fbfa98977' &&
            $data['stepName'] === 'Регистрация') {
            $isRequiredSmsCode = true;
        }

        if (($data['paymentModeCode']['code'] ?? null) === 4 &&
            $data['hypothecBankId'] === '5f399d29-60b9-40f6-8d1a-935fbfa98977' &&
            $data['stepName'] === 'Регистрация') {
            $smsCodeType = 'hypsber';
        } elseif ($data['escrowBankId']??null === 'd417af4e-bc76-e711-9402-005056bf3b92') {
            $smsCodeType = 'domrf';
        } else {
            $smsCodeType = null;
        }

        $draftContractDocumentInfo = '';

        if (($data['paymentModeCode']['code'] ?? null) === 4) {
            $documents = $this->documentRepository->getContractDocumentsWithTypeCode($data['id'], [128]);

            if (count($documents) > 0) {
                $document = collect($documents)->first();
            } else {
                $documents = $this->documentRepository->getContractDocumentsWithTypeCode($data['id'], [1024]);
                if (count($documents) > 0) {
                    $document = collect($documents)->first();
                } else {
                    $document = null;
                }
            }
        } else {
            $documents = [];

            if (isset($data['id'])) {
                $documents = $this->documentRepository->getContractDocumentsWithTypeCode($data['id'], [1024]);
            }
            if (count($documents) > 0) {
                $document = collect($documents)->first();
            } else {
                $document = null;
            }
        }

        if (($data['paymentModeCode']['code'] ?? null) === 4) {
            if ($document?->getType()?->value === 1024) {
                $draftContractDocumentInfo = 'Для вас подготовлен проект договора, пожалуйста, ознакомьтесь с условиями. После будет подготовлен проект договора с правками банка, c которым также будет необходимо ознакомиться.';
            } elseif ($document?->getType()?->value === 128) {
                $draftContractDocumentInfo = 'Для вас подготовлен проект договора, пожалуйста, ознакомьтесь с условиями.';
            }
        } else {
            $draftContractDocumentInfo = 'Для вас подготовлен проект договора, пожалуйста, ознакомьтесь с условиями.';
        }

        if (($data['paymentModeCode']['code'] ?? null) === 4) {
            $isHypothecSup = true;
        } else {
            $isHypothecSup = false;
        }

        return new Contract(
            id: $data['id'],
            name: $data['name'],
            group: isset($data['contractGroup']) ?
                ContractGroup::from($data['contractGroup']) : ContractGroup::contract(),
            date: isset($data['contractDate']) ? new Carbon($data['contractDate']) : null,
            estimated: $data['estimated'] ?? null,
            estimatedWoBTI: $data['estimatedWoBTI'] ?? null,
            serviceId: $data['serviceMainId'] ?? null,
            service: ContractService::tryFrom($data['serviceMain']['code'] ?? ''),
            status: ContractStatus::tryFrom($data['status']['code'] ?? ''),
            stepName: $data['stepName'] ?? null,
            debtPlanSum: $data['debtPlanSum'] ?? null,
            percentPay: $data['percentPay'] ?? null,
            percentPayWoBTI: $data['percentPayWoBTI'] ?? null,
            registrationFilingDate: isset($data['registrationFilingDate']) ?
                new Carbon($data['registrationFilingDate']) : null,
            registrationDate: isset($data['registrationDate']) ? new Carbon($data['registrationDate']) : null,
            paymentPlans: $paymentPlans,
            payments: $payments,
            jointOwners: $jointOwners ?? null,
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
            contractInfo: $contractInfo,
            ownership: $ownership ?? null,
            salesScheme: $salesScheme ?? null,
            isContractApprove: $isDocumentApprove,
            property: $property,
            isSignAppAvailible: '',
            electroReg: $electroReg,
            electroRegInfo: $electroRegInfo,
            stages: $stages,
            isRequiredSmsCode: $isRequiredSmsCode,
            smsCodeType: $smsCodeType,
            draftContractDocument: $document,
            draftContractDocumentInfo: $draftContractDocumentInfo,
            delayDays: $data['delayDays'] ?? null,
            registrationNumber: $data['registrationNumber'] ?? null,
            bankDate: isset($data['bankDate']) ? new Carbon($data['bankDate']) : null,
            branchAddressHb: $data['branchAddressHb'] ?? null,
            bankManagerEmail: $data['bankManagerEmail'] ?? null,
            bankManagerMobilePhone: $data['bankManagerMobilePhone'] ?? null,
            bankManagerFullName: $data['bankManagerFullName'] ?? null,
            isHypothecSup: $isHypothecSup,
            opportunityMainId: $data['opportunityMainId'] ?? null,
            esValidityDate: isset($data['esValidityDate']) ? new Carbon($data['esValidityDate']) : null,
            isDigitalTransaction: $data['isDigitalTransaction'] ?? null,
            contractsCount: $data['contractsCount'] ?? null,
        );
        // phpcs:enable
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function makeContractV2(array $data, User $user): Contract
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

        // phpcs:disable
        $jointOwners = [];

        if (isset($data['jointOwners'])) {
            foreach ($data['jointOwners'] as $jointOwner) {
                if (
                    isset($jointOwner['roleCode']['code']) &&
                    $jointOwner['roleCode']['code'] === 1 &&
                    ($jointOwner['customerType']['code'] ?? null) != 1
                ) {
                    $jointOwners[] = $this->customerRepository->makeCustomer($jointOwner);
                } elseif (
                    ($jointOwner['customerType']['code'] ?? null) == 1
                ) {
                    if (!isset($jointOwner['confidant'])) {
                        $jointOwners[] = $this->customerContractConfidant->makeCustomer(
                            $jointOwner,
                            $customer = []
                        );
                    } else {
                        $customer = $this->dynamicsCrmClient->getCustomerById(
                            $jointOwner['primaryContact']['code']
                        );
                        $jointOwners[] = $this->customerContractConfidant->makeCustomer(
                            $jointOwner,
                            $customer
                        );
                    }
                }
            }
        }
        // phpcs:enable

        $property = null;
        if (isset($data['articleOrders'][0])) {
            $property = $this->propertyRepository->getById($data['articleOrders'][0]['articleId']);
        }

        $ownership = null;

        if (isset($data['jointOwners'])) {
            // phpcs:disable
            $personalOwnerships = collect($data['jointOwners'])->where('ownerType.code', '=', OwnerType::personal()->value);
            $sharedOwnerships = collect($data['jointOwners'])->where('ownerType.code', '=', OwnerType::shared()->value);
            $jointOwnerships = collect($data['jointOwners'])->where('ownerType.code', '=', OwnerType::joint()->value);
            // phpcs:enable

            if ($personalOwnerships->count() !== 0) {
                $ownership = $this->ownershipRepository->makeOwnership([
                    'code' => OwnerType::personal()->value,
                    'name' => OwnerType::personal()->label,
                ]);
            } elseif ($sharedOwnerships->count() !== 0) {
                $ownership = $this->ownershipRepository->makeOwnership([
                    'code' => OwnerType::shared()->value,
                    'name' => OwnerType::shared()->label,
                ]);
            } elseif ($jointOwnerships->count() !== 0) {
                $ownership = $this->ownershipRepository->makeOwnership([
                    'code' => OwnerType::joint()->value,
                    'name' => OwnerType::joint()->label,
                ]);
            }
        }

        $articleOrders = [];

        if (isset($data['articleOrders'])) {
            foreach ($data['articleOrders'] as $articleOrder) {
                $articleOrders[] = $this->articleOrderRepository->makeArticleOrder($articleOrder);
            }
        }

        $salesScheme = null;
        $articleCodes = ['020020', '020030', '020050', '020011', '020080', '020040', '020010'];

        foreach ($articleOrders as $articleOrder) {
            if (in_array($articleOrder->getCode(), $articleCodes)) {
                $salesScheme = $articleOrder;
            }
        }

        $contractInfo = $this->getContractInfo($data);

        // phpcs:disable
        $electroReg = 'no';
        $electroRegInfo = null;

        if (isset($data['electroReg'])) {
            if ($data['electroReg'] == true) {
                $electroReg = 'yes';
                $electroRegInfo = null;
            } elseif ($data['electroReg'] == false) {
                $electroReg = 'no';
                $electroRegInfo = 'Сделка проходит не в электронном формате. Подписание договора будет проходить в офисе Пионера';
            }
        }

        if (($data['hypothecBankId'] ?? null) === '5f399d29-60b9-40f6-8d1a-935fbfa98977') {
            $electroReg = 'sber';
            $electroRegInfo = 'Формированием электронной подписи занимается менеджер банка. Подписание договора будет проходить через платформу электронной регистрации Сбербанка — ДомКлик посредством 4-значного кода из смс сообщения, которое направит менеджер Пионера';
        } elseif (($data['hypothecBankId'] ?? null) === '3e45c241-ea79-ed11-bbad-005056bfae62') {
            $electroReg = 'tinkoff';
            $electroRegInfo = 'Формированием электронной подписи занимается менеджер банка. Подписание договора будет проходить в мобильном приложении Тинькофф';
        }

        $isDocumentApprove = null;
        $documentsApprove = $this->documentRepository->getContractDocumentsWithTypeCode($data['id'], [128,1024,2048]);

        if (count($documentsApprove) !== 0) {
            $fk = array_key_first($documentsApprove);
            $isDocumentApprove = $documentsApprove[$fk]->getIsDocumentApprove();
        }

        try {
            $contracts = $this->getContractsByTypes('2050,65536,100000001,2097152', $user->crm_id);
        } catch (\Throwable $exception) {
            $contracts = [];
        }

        $stages = $this->stagesRepository->makeContractStagesV2(demand: $data, contracts: $contracts, isContractApprove: $isDocumentApprove, property: $property);

        $isRequiredSmsCode = false;

        if (($data['paymentModeCode']['code'] ?? null) === 4 &&
            $data['hypothecBankId'] === '5f399d29-60b9-40f6-8d1a-935fbfa98977' &&
            $data['stepName'] === 'Регистрация') {
            $isRequiredSmsCode = true;
        }

        if (($data['paymentModeCode']['code'] ?? null) === 4 &&
            $data['hypothecBankId'] === '5f399d29-60b9-40f6-8d1a-935fbfa98977' &&
            $data['stepName'] === 'Регистрация') {
            $smsCodeType = 'hypsber';
        } elseif ($data['escrowBankId'] ?? null === 'd417af4e-bc76-e711-9402-005056bf3b92') {
            $smsCodeType = 'domrf';
        } else {
            $smsCodeType = null;
        }

        $draftContractDocumentInfo = '';

        if (($data['paymentModeCode']['code'] ?? null) === 4) {
            $documents = $this->documentRepository->getContractDocumentsWithTypeCode($data['id'], [128]);

            if (count($documents) > 0) {
                $document = collect($documents)->first();
            } else {
                $documents = $this->documentRepository->getContractDocumentsWithTypeCode($data['id'], [1024]);
                if (count($documents) > 0) {
                    $document = collect($documents)->first();
                } else {
                    $document = null;
                }
            }
        } else {
            $documents = [];

            if (isset($data['id'])) {
                $documents = $this->documentRepository->getContractDocumentsWithTypeCode($data['id'], [1024]);
            }

            if (count($documents) > 0) {
                $document = collect($documents)->first();
            } else {
                $document = null;
            }
        }

        if (($data['paymentModeCode']['code'] ?? null) === 4) {
            if ($document->getType()->value === 1024) {
                $draftContractDocumentInfo = 'Для вас подготовлен проект договора, пожалуйста, ознакомьтесь с условиями. После будет подготовлен проект договора с правками банка, c которым также будет необходимо ознакомиться.';
            } elseif ($document->getType()->value === 128) {
                $draftContractDocumentInfo = 'Для вас подготовлен проект договора, пожалуйста, ознакомьтесь с условиями.';
            }
        } else {
            $draftContractDocumentInfo = 'Для вас подготовлен проект договора, пожалуйста, ознакомьтесь с условиями.';
        }

        if ($data['paymentModeCode']['code'] ?? null === 4) {
            $isHypothecSup = true;
        } else {
            $isHypothecSup = false;
        }
        return new Contract(
            id: $data['id'],
            name: $data['name'],
            group: isset($data['contractGroup']) ?
                ContractGroup::from($data['contractGroup']) : ContractGroup::contract(),
            date: isset($data['contractDate']) ? new Carbon($data['contractDate']) : null,
            estimated: $data['estimated'] ?? null,
            estimatedWoBTI: $data['estimatedWoBTI'] ?? null,
            serviceId: $data['serviceMainId'] ?? null,
            service: ContractService::tryFrom($data['serviceMain']['code'] ?? ''),
            status: ContractStatus::tryFrom($data['status']['code'] ?? ''),
            stepName: $data['stepName'] ?? null,
            debtPlanSum: $data['debtPlanSum'] ?? null,
            percentPay: $data['percentPay'] ?? null,
            percentPayWoBTI: $data['percentPayWoBTI'] ?? null,
            registrationFilingDate: isset($data['registrationFilingDate']) ?
                new Carbon($data['registrationFilingDate']) : null,
            registrationDate: isset($data['registrationDate']) ? new Carbon($data['registrationDate']) : null,
            paymentPlans: $paymentPlans,
            payments: $payments,
            jointOwners: $jointOwners ?? null,
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
            contractInfo: $contractInfo,
            ownership: $ownership ?? null,
            salesScheme: $salesScheme ?? null,
            isContractApprove: $isDocumentApprove,
            property: $property,
            isSignAppAvailible: '',
            electroReg: $electroReg,
            electroRegInfo: $electroRegInfo,
            stages: $stages,
            isRequiredSmsCode: $isRequiredSmsCode,
            smsCodeType: $smsCodeType,
            draftContractDocument: $document,
            draftContractDocumentInfo: $draftContractDocumentInfo,
            delayDays: $data['delayDays'] ?? null,
            registrationNumber: $data['registrationNumber'] ?? null,
            bankDate: isset($data['bankDate']) ? new Carbon($data['bankDate']) : null,
            branchAddressHb: $data['branchAddressHb'] ?? null,
            bankManagerEmail: $data['bankManagerEmail'] ?? null,
            bankManagerMobilePhone: $data['bankManagerMobilePhone'] ?? null,
            bankManagerFullName: $data['bankManagerFullName'] ?? null,
            isHypothecSup: $isHypothecSup,
            opportunityMainId: $data['opportunityMainId'] ?? null,
            esValidityDate: isset($data['esValidityDate']) ? new Carbon($data['esValidityDate']) : null,
            isDigitalTransaction: $data['isDigitalTransaction'] ?? null,
            contractsCount: $data['contractsCount'] ?? null,
        );
        // phpcs:enable
    }


    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function getAddDraftById(string $id): array
    {
        return $this->documentRepository->getContractDocumentsWithCode(3, $id, [16, 256, 512, 1024]);
    }

    private function getContractInfo($contract): ?array
    {
        $contract_info = null;

        if (isset($contract['serviceMain'])) {
            if (($contract['serviceMain']['code'] === '020020') && ($contract['estimated'] ?? null) !== 0) {
                $contract_info = [
                    'id' => null,
                    'main_id' => $contract['id'],
                    'type' => null
                ];
            }

            if ($contract['serviceMain']['code'] === '020030') {
                $contract_info = [
                    'id' => $contract['id'],
                    'main_id' => $contract['opportunityMainId'],
                    'type' => 'presale'
                ];
            }

            if ($contract['serviceMain']['code'] === '020011') {
                $contract_info = [
                    'id' => $contract['id'],
                    'main_id' => $contract['opportunityMainId'],
                    'type' => 'transfer'
                ];
            }

            if ($contract['serviceMain']['code'] === '020040') {
                $contract_info = [
                    'id' => $contract['opportunityMainId'],
                    'main_id' => $contract['id'],
                    'type' => 'transfer'
                ];
            }

            if ($contract['serviceMain']['code'] === '020010') {
                $contract_info = [
                    'id' => $contract['id'],
                    'main_id' => $contract['opportunityMainId'],
                    'type' => 'transfer'
                ];
            }

            if ($contract['serviceMain']['code'] === '020080') {
                $contract_info = [
                    'id' => $contract['id'],
                    'main_id' => $contract['opportunityMainId'],
                    'type' => null
                ];
            }
        }

        return $contract_info;
    }
}
