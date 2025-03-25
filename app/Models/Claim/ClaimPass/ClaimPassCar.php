<?php

namespace App\Models\Claim\ClaimPass;

/**
 * Class ClaimPassCar
 *
 * @package App\Models\Claim\ClaimPass
 */
class ClaimPassCar
{
    public function __construct(
        private ClaimPassCarType $carType,
        private string $number,
    ) {
    }

    public function getCarType(): ClaimPassCarType
    {
        return $this->carType;
    }

    public function getNumber(): string
    {
        return $this->number;
    }
}
