<?php

namespace App\Models\Meter;

/**
 * Class MeterTariffValue
 *
 * @package App\Models\Meter
 */
class MeterTariffValue
{
    public function __construct(
        private string $subtype,
        private string $cost,
    ) {
    }

    public function getSubtype(): string
    {
        return $this->subtype;
    }

    public function getCost(): string
    {
        return $this->cost;
    }
}
