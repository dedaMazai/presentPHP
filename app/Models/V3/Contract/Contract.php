<?php

namespace App\Models\V3\Contract;

use App\Models\Document\Document;
use App\Models\V2\Contract\ContractGroup;
use App\Models\V2\Contract\ContractService;
use App\Models\V2\Contract\ContractStatus;
use App\Models\V2\Sales\ArticleOrder;
use App\Models\Sales\CharacteristicSale\CharacteristicSale;
use App\Models\Sales\Customer\Customer;
use App\Models\Sales\Owner;
use App\Models\Sales\Ownership;
use App\Models\Sales\Payment;
use App\Models\Sales\PaymentMode;
use App\Models\Sales\PaymentPlan;
use App\Models\V2\Sales\Property\Property;
use Carbon\Carbon;

/**
 * Class Contract
 *
 * @package App\Models\Contract
 */
class Contract
{
    /**
     * @param string                  $id
     * @param string                  $name
     * @param ContractGroup           $group
     * @param Carbon|null             $date
     * @param float|null              $estimated
     * @param float|null              $estimatedWoBTI
     * @param string|null             $serviceId
     * @param ContractService|null    $service
     * @param ContractStatus|null     $status
     * @param string|null             $stepName
     * @param float|null              $debtPlanSum
     * @param float|null              $percentPay
     * @param float|null              $percentPayWoBTI
     * @param Carbon|null             $registrationFilingDate
     * @param Carbon|null             $registrationDate
     * @param PaymentPlan[]           $paymentPlans
     * @param Payment[]               $payments
     * @param Customer[]              $jointOwners
     * @param ArticleOrder[]          $articleOrders
     * @param string|null             $creditNumber
     * @param Carbon|null             $creditDate
     * @param Owner|null              $owner
     * @param string|null             $demandId
     * @param Carbon|null             $transferDeedDate
     * @param string|null             $registrationStage
     * @param string|null             $hypothecBankId
     * @param string|null             $letterOfCreditBankId
     * @param Carbon|null             $dateOfSigningFact
     * @param Carbon|null             $receiptData
     * @param Carbon|null             $modifiedOn
     * @param Carbon|null             $dateOfSigningPlan
     * @param bool|null               $letterOfCreditStatus
     * @param float|null              $sumDiscount
     * @param PaymentMode|null        $paymentModeCode
     * @param CharacteristicSale|null $baseFinishVariant
     * @param string|null             $personalAccount
     */
    public function __construct(
        private string $id,
        private string $name,
        private ?ContractGroup $group,
        private ?Carbon $date,
        private ?float $estimated,
        private ?float $estimatedWoBTI,
        private ?string $serviceId,
        private ?ContractService $service,
        private ?ContractStatus $status,
        private ?string $stepName,
        private ?float $debtPlanSum,
        private ?float $percentPay,
        private ?float $percentPayWoBTI,
        private ?Carbon $registrationFilingDate,
        private ?Carbon $registrationDate,
        private ?array $paymentPlans,
        private ?array $payments,
        private array $jointOwners,
        private array $articleOrders,
        private ?string $creditNumber,
        private ?Carbon $creditDate,
        private ?Owner $owner,
        //        private ?CharacteristicSale $baseFinishVariant,
        private ?string $demandId,
        private ?Carbon $transferDeedDate,
        private ?string $registrationStage,
        private ?string $hypothecBankId,
        private ?string $letterOfCreditBankId,
        private ?Carbon $dateOfSigningFact,
        private ?Carbon $receiptData,
        private ?Carbon $modifiedOn,
        private ?Carbon $dateOfSigningPlan,
        private ?bool $letterOfCreditStatus,
        private ?float $sumDiscount,
        private ?PaymentMode $paymentModeCode,
        private ?CharacteristicSale $baseFinishVariant,
        private ?string $personalAccount,
        private ?array $contractInfo,
        private ?Ownership $ownership,
        private ?ArticleOrder $salesScheme,
        private ?bool $isContractApprove,
        private ?Property $property,
        private ?bool $isSignAppAvailible,
        private ?string $electroReg,
        private ?string $electroRegInfo,
        private ?array $stages,
        private ?bool $isRequiredSmsCode,
        private ?string $smsCodeType,
        private ?Document $draftContractDocument,
        private ?string $draftContractDocumentInfo,
        private ?int $delayDays,
        private ?string $registrationNumber,
        private ?Carbon $bankDate,
        private ?string $branchAddressHb,
        private ?string $bankManagerEmail,
        private ?string $bankManagerMobilePhone,
        private ?string $bankManagerFullName,
        private ?bool $isHypothecSup,
        private ?string $opportunityMainId,
        private ?Carbon $esValidityDate,
        private ?bool $isDigitalTransaction,
        private ?int $contractsCount,
        private ?array $customerType,
        private ?bool $hide_uk_documents,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getGroup(): ContractGroup
    {
        return $this->group;
    }

    public function getDate(): ?Carbon
    {
        return $this->date;
    }

    public function getEstimated(): ?float
    {
        return $this->estimated;
    }

    public function getEstimatedWoBTI(): ?float
    {
        return $this->estimatedWoBTI;
    }

    public function getServiceId(): ?string
    {
        return $this->serviceId;
    }

    public function getService(): ?ContractService
    {
        return $this->service;
    }

    public function getStatus(): ?ContractStatus
    {
        return $this->status;
    }

    public function getStepName(): ?string
    {
        return $this->stepName;
    }

    public function getDebtPlanSum(): ?float
    {
        return $this->debtPlanSum;
    }

    public function getPercentPay(): ?float
    {
        return $this->percentPay;
    }

    public function getPercentPayWoBTI(): ?float
    {
        return $this->percentPayWoBTI;
    }

    public function getRegistrationFilingDate(): ?Carbon
    {
        return $this->registrationFilingDate;
    }

    public function getRegistrationDate(): ?Carbon
    {
        return $this->registrationDate;
    }

    /**
     * @return PaymentPlan[]
     */
    public function getPaymentPlans(): array
    {
        return $this->paymentPlans;
    }

    /**
     * @return Payment[]
     */
    public function getPayments(): array
    {
        return $this->payments;
    }

    /**
     * @return Customer[]
     */
    public function getJointOwners(): array
    {
        return $this->jointOwners;
    }

    /**
     * @return ArticleOrder[]
     */
    public function getArticleOrders(): array
    {
        return $this->articleOrders;
    }

    public function getCreditNumber(): ?string
    {
        return $this->creditNumber;
    }

    public function getCreditDate(): ?Carbon
    {
        return $this->creditDate;
    }

    public function getOwner(): ?Owner
    {
        return $this->owner;
    }

    public function getDemandId(): ?string
    {
        return $this->demandId;
    }

    public function getTransferDeedDate(): ?Carbon
    {
        return $this->transferDeedDate;
    }

    public function getRegistrationStage(): ?string
    {
        return $this->registrationStage;
    }

    public function getHypothecBankId(): ?string
    {
        return $this->hypothecBankId;
    }

    public function getLetterOfCreditBankId(): ?string
    {
        return $this->letterOfCreditBankId;
    }

    public function getDateOfSigningFact(): ?Carbon
    {
        return $this->dateOfSigningFact;
    }

    public function getReceiptData(): ?Carbon
    {
        return $this->receiptData;
    }

    public function getModifiedOn(): ?Carbon
    {
        return $this->modifiedOn;
    }

    public function getDateOfSigningPlan(): ?Carbon
    {
        return $this->dateOfSigningPlan;
    }

    public function getLetterOfCreditStatus(): ?bool
    {
        return $this->letterOfCreditStatus;
    }

    public function getSumDiscount(): ?float
    {
        return $this->sumDiscount;
    }

    public function getPaymentModeCode(): ?PaymentMode
    {
        return $this->paymentModeCode;
    }

    public function getBaseFinishVariant(): ?CharacteristicSale
    {
        return $this->baseFinishVariant;
    }

    public function getEstimatedKop(): ?int
    {
        if ($this->estimated) {
            $estimated = number_format($this->estimated, 2, '.', '');

            return intval(str_replace('.', '', $estimated));
        } else {
            return null;
        }
    }

    public function getPersonalAccount(): ?string
    {
        return $this->personalAccount;
    }

    /**
     * @return array|null
     */
    public function getContractInfo(): ?array
    {
        return $this->contractInfo;
    }

    public function getOwnership(): ?Ownership
    {
        return $this->ownership;
    }

    public function getSalesScheme(): ?ArticleOrder
    {
        return $this->salesScheme;
    }

    public function getIsContractApprove(): ?bool
    {
        return $this->isContractApprove;
    }

    public function getProperty(): ?Property
    {
        return $this->property;
    }

    public function getIsSignAppAvailible(): ?bool
    {
        return $this->isSignAppAvailible;
    }

    public function getElectroReg(): ?string
    {
        return $this->electroReg;
    }

    public function getElectroRegInfo(): ?string
    {
        return $this->electroRegInfo;
    }

    public function getStages(): ?array
    {
        return $this->stages;
    }

    public function getIsRequiredSmsCode(): ?bool
    {
        return $this->isRequiredSmsCode;
    }

    public function getSmsCodeType(): ?string
    {
        return $this->smsCodeType;
    }

    public function getDraftContractDocument(): ?Document
    {
        return $this->draftContractDocument;
    }

    public function getDraftContractDocumentInfo(): ?string
    {
        return $this->draftContractDocumentInfo;
    }

    public function getDelayDays(): ?int
    {
        return $this->delayDays;
    }

    public function getRegistrationNumber(): ?string
    {
        return $this->registrationNumber;
    }

    public function getBranchAddressHb(): ?string
    {
        return $this->branchAddressHb;
    }

    public function getBankManagerEmail(): ?string
    {
        return $this->bankManagerEmail;
    }

    public function getBankManagerMobilePhone(): ?string
    {
        return $this->bankManagerMobilePhone;
    }

    public function getBankManagerFullName(): ?string
    {
        return $this->bankManagerFullName;
    }

    public function getBankDate(): ?Carbon
    {
        return $this->bankDate;
    }

    public function getIsHypothecSup(): ?bool
    {
        return $this->isHypothecSup;
    }

    public function getOpportunityMainId(): ?string
    {
        return $this->opportunityMainId;
    }

    public function getEsValidityDate(): ?Carbon
    {
        return $this->esValidityDate;
    }

    public function getIsDigitalTransaction(): ?bool
    {
        return $this->isDigitalTransaction;
    }

    public function getContractsCount(): ?int
    {
        return $this->contractsCount;
    }

    public function getCustomerType(): ?array
    {
        return $this->customerType;
    }

    public function getHideUkDocuments(): ?bool
    {
        return $this->hide_uk_documents;
    }
}
