<?php

namespace App\Models\Meter;

use Carbon\Carbon;

/**
 * Class Meter
 *
 * @package App\Models\Meter
 */
class Meter
{
    /**
     * @param string             $id
     * @param MeterType          $type
     * @param MeterSubtype|null  $subtype
     * @param MeterDataInputType $dataInputType
     * @param string             $number
     * @param bool|null          $isPreviousValueCalculatedByStandard
     * @param bool|null          $isValuesEnteredInCurrentPeriod
     * @param Carbon|null        $dateVerification
     * @param MeterValue[]       $values
     * @param MeterName|null     $name
     */
    public function __construct(
        private string $id,
        private MeterType $type,
        private ?MeterSubtype $subtype,
        private MeterDataInputType $dataInputType,
        private string $number,
        private ?bool $isPreviousValueCalculatedByStandard,
        private ?bool $isValuesEnteredInCurrentPeriod,
        private ?Carbon $dateVerification,
        private array $values,
        private ?MeterName $name = null,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getType(): MeterType
    {
        return $this->type;
    }

    public function getSubtype(): ?MeterSubtype
    {
        return $this->subtype;
    }

    public function getDataInputType(): MeterDataInputType
    {
        return $this->dataInputType;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getIsPreviousValueCalculatedByStandard(): ?bool
    {
        return $this->isPreviousValueCalculatedByStandard;
    }

    public function getIsValuesEnteredInCurrentPeriod(): ?bool
    {
        return $this->isValuesEnteredInCurrentPeriod;
    }

    public function getDateVerification(): ?Carbon
    {
        return $this->dateVerification;
    }

    /**
     * @return MeterValue[]
     */
    public function getValues(): array
    {
        return $this->values;
    }

    public function getName(): ?MeterName
    {
        return $this->name;
    }
}
