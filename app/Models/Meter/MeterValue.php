<?php

namespace App\Models\Meter;

/**
 * Class MeterValue
 *
 * @package App\Models\Meter
 */
class MeterValue
{
    public function __construct(
        private string $tariffId,
        private string $tariffName,
        private ?float $currentValue,
        private float $previousValue,
    ) {
    }

    public function getTariffId(): string
    {
        return $this->tariffId;
    }

    public function getTariffName(): string
    {
        return $this->tariffName;
    }

    public function getCurrentValue(): ?float
    {
        return $this->currentValue;
    }

    public function getPreviousValue(): float
    {
        return $this->previousValue;
    }
}
