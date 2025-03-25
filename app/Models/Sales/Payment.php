<?php

namespace App\Models\Sales;

use Carbon\Carbon;

/**
 * Class Payment
 *
 * @package App\Models\Sales
 */
class Payment
{
    public function __construct(
        private string $id,
        private string $number,
        private Carbon $date,
        private float $sum,
        private ?string $assignment,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getNumber(): string
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

    public function getAssignment(): ?string
    {
        return $this->assignment;
    }
}
