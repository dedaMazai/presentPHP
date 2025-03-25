<?php

namespace App\Models\Meter;

/**
 * Class MeterStatistics
 *
 * @package App\Models\Meter
 */
class MeterStatistics
{
    /**
     * @param MeterType              $type
     * @param MeterSubtype|null      $subtype
     * @param string|null            $unit
     * @param float                  $total
     * @param float                  $average
     * @param MeterStatisticsValue[] $statistics
     */
    public function __construct(
        private MeterType $type,
        private ?MeterSubtype $subtype,
        private ?string $unit,
        private float $total,
        private float $average,
        private array $statistics,
    ) {
    }

    public function getType(): MeterType
    {
        return $this->type;
    }

    public function getSubtype(): ?MeterSubtype
    {
        return $this->subtype;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
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
     * @return MeterStatisticsValue[]
     */
    public function getStatistics(): array
    {
        return $this->statistics;
    }
}
