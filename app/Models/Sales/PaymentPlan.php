<?php

namespace App\Models\Sales;

use Carbon\Carbon;

/**
 * Class PaymentPlan
 *
 * @package App\Models\Sales
 */
class PaymentPlan
{
    public function __construct(
        private string $id,
        private int $number,
        private Carbon $date,
        private float $sum,
        private ?float $sumPayment,
        private ?float $sumDebt,
        private ?string $assignment,
        private ?int $signPay,
        private ?int $numberDaysDelay,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getDate(): Carbon
    {
        return $this->date;
    }

    public function getSum(): float
    {
        return $this->sum;
    }

    public function getSumPayment(): ?float
    {
        return $this->sumPayment;
    }

    public function getSumDebt(): ?float
    {
        return $this->sumDebt;
    }

    public function getAssignment(): ?string
    {
        return $this->assignment;
    }

    public function getSignPay(): ?int
    {
        return $this->signPay;
    }

    public function getNumberDaysDelay(): ?int
    {
        return $this->numberDaysDelay;
    }
}
