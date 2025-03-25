<?php

namespace App\Models\Meter;

/**
 * Class MeterStatisticsValue
 *
 * @package App\Models\Meter
 */
class MeterStatisticsValue
{
    /**
     * @param string  $tariff
     * @param float   $total
     * @param float   $average
     * @param float[] $data
     */
    public function __construct(
        private string $tariff,
        private float $total,
        private float $average,
        private array $data,
    ) {
    }

    public function getTariff(): string
    {
        return $this->tariff;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function getAverage(): float
    {
        return $this->average;
    }

    /**
     * @return float[]
     */
    public function getData(): array
    {
        return $this->data;
    }
}
