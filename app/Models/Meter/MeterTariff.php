<?php

namespace App\Models\Meter;

/**
 * Class MeterTariff
 *
 * @package App\Models\Meter
 */
class MeterTariff
{
    /**
     * @param string             $type
     * @param string|null        $name
     * @param string             $unit
     * @param MeterTariffValue[] $values
     */
    public function __construct(
        private string $type,
        private ?string $name,
        private string $unit,
        private array $values,
    ) {
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    /**
     * @return MeterTariffValue[]
     */
    public function getValues(): array
    {
        return $this->values;
    }
}
