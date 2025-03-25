<?php

namespace App\Models\Sales\MortgageApproval;

/**
 * Class MortgageApproval
 *
 * @package App\Models\Sales
 */
class MortgageApproval
{
    public function __construct(
        private string $id,
        private ?string $name,
        private ?string $bankId,
        private ?string $bankName,
        private ?MortgageApprovalDecisionType $decisionType,
        private ?float $rate,
        private ?int $period,
        private ?float $initialPayment,
        private ?string $creditingPeriodApproved,
        private ?float $rateApproved,
        private ?float $monthlyPaymentApproved,
        private ?string $dhStatusCode,
        private ?float $sumApproved,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getBankId(): ?string
    {
        return $this->bankId;
    }

    public function getBankName(): ?string
    {
        return $this->bankName;
    }

    public function getDecisionType(): ?MortgageApprovalDecisionType
    {
        return $this->decisionType;
    }

    public function getRate(): ?float
    {
        return $this->rate;
    }

    public function getPeriod(): ?int
    {
        return $this->period;
    }

    public function getInitialPayment(): ?float
    {
        return $this->initialPayment;
    }

    public function getCreditingPeriodApproved(): ?string
    {
        return $this->creditingPeriodApproved;
    }

    public function getRateApproved(): ?float
    {
        return $this->rateApproved;
    }

    public function getMonthlyPaymentApproved(): ?float
    {
        return $this->monthlyPaymentApproved;
    }

    public function getDhStatusCode(): ?string
    {
        return $this->dhStatusCode;
    }

    public function getSumApproved(): ?float
    {
        return $this->sumApproved;
    }
}
