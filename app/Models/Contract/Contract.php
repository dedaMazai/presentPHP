<?php

namespace App\Models\Contract;

use App\Models\Sales\ArticleOrder;
use App\Models\Sales\CharacteristicSale\CharacteristicSale;
use App\Models\Sales\Customer\Customer;
use App\Models\Sales\Owner;
use App\Models\Sales\Payment;
use App\Models\Sales\PaymentMode;
use App\Models\Sales\PaymentPlan;
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
     * @param string|null             $serviceId
     * @param ContractService|null    $service
     * @param ContractStatus|null     $status
     * @param string|null             $stepName
     * @param float|null              $debtPlanSum
     * @param float|null              $percentPay
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
        private readonly string              $id,
        private readonly string              $name,
        private readonly ContractGroup       $group,
        private readonly ?Carbon             $date,
        private readonly ?float              $estimated,
        private readonly ?string             $serviceId,
        private readonly ?ContractService    $service,
        private readonly ?ContractStatus     $status,
        private readonly ?string             $stepName,
        private readonly ?float              $debtPlanSum,
        private readonly ?float              $percentPay,
        private readonly ?Carbon             $registrationFilingDate,
        private readonly ?Carbon             $registrationDate,
        private readonly ?string             $registrationNumber,
        private readonly array               $paymentPlans,
        private readonly array               $payments,
        private readonly array               $jointOwners,
        private readonly array               $articleOrders,
        private readonly ?string             $creditNumber,
        private readonly ?Carbon             $creditDate,
        private readonly ?Owner              $owner,
        //private readonly ?CharacteristicSale $baseFinishVariant,
        private readonly ?string             $demandId,
        private readonly ?Carbon             $transferDeedDate,
        private readonly ?string             $registrationStage,
        private readonly ?string             $hypothecBankId,
        private readonly ?string             $letterOfCreditBankId,
        private readonly ?Carbon             $dateOfSigningFact,
        private readonly ?Carbon             $receiptData,
        private readonly ?Carbon             $modifiedOn,
        private readonly ?Carbon             $dateOfSigningPlan,
        private readonly ?bool               $letterOfCreditStatus,
        private readonly ?float              $sumDiscount,
        private readonly ?PaymentMode        $paymentModeCode,
        private readonly ?CharacteristicSale $baseFinishVariant,
        private readonly ?string             $personalAccount,
        private readonly ?string             $depositorFizId,
        private readonly ?string             $depositorUrId,
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

            return (int)str_replace('.', '', $estimated);
        }

        return null;
    }

    public function getPersonalAccount(): ?string
    {
        return $this->personalAccount;
    }

    public function getRegistrationNumber(): ?string
    {
        return $this->registrationNumber;
    }

    public function getDepositorFizId(): ?string
    {
        return $this->depositorFizId;
    }

    public function getDepositorUrId(): ?string
    {
        return $this->depositorUrId;
    }
}
