<?php

namespace App\Models\Account;

use App\Models\Building\Building;
use App\Models\Meter\MeterEnterPeriod;
use App\Models\UkProject;

/**
 * Class Account
 *
 * @package App\Models\Account
 */
class Account
{
    public function __construct(
        private readonly string               $number,
        private readonly AccountRealtyType    $realtyType,
        private readonly string               $address,
        private readonly string               $addressId,
        private readonly string               $address1cId,
        private readonly int                  $balance,
        private readonly int                  $servicesDebt,
        private readonly AccountServiceSeller $serviceSeller,
        private readonly int                  $notPaidMonths,
        private readonly bool                 $isMeterEnterPeriodActive,
        private readonly ?MeterEnterPeriod    $meterEnterPeriod,
        private readonly ?int                 $floor,
        private readonly ?string              $addressNumber,
        private readonly ?int                 $rooms,
        private readonly ?float               $totalArea,
        private readonly ?float               $livingArea,
        private readonly ?int                 $metersCount,
        private readonly ?UkProject           $ukProject,
        private readonly ?Building            $buildZid,
        private readonly ?int                 $buildId,
        private readonly ?string              $ukEmergencyClaimPhone,
        private readonly ?string              $classifierUKId,
    ) {
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getRealtyType(): AccountRealtyType
    {
        return $this->realtyType;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getAddressId(): string
    {
        return $this->addressId;
    }

    public function getAddress1cId(): string
    {
        return $this->address1cId;
    }

    public function getBalance(): int
    {
        return $this->balance;
    }

    public function getServicesDebt(): int
    {
        return $this->servicesDebt;
    }

    public function getServiceSeller(): AccountServiceSeller
    {
        return $this->serviceSeller;
    }

    public function getNotPaidMonths(): int
    {
        return $this->notPaidMonths;
    }

    public function getIsMeterEnterPeriodActive(): bool
    {
        return $this->isMeterEnterPeriodActive;
    }

    public function getMeterEnterPeriod(): ?MeterEnterPeriod
    {
        return $this->meterEnterPeriod;
    }

    public function getFloor(): ?int
    {
        return $this->floor;
    }

    public function getAddressNumber(): ?string
    {
        return $this->addressNumber;
    }

    public function getRooms(): ?int
    {
        return $this->rooms;
    }

    public function getTotalArea(): ?float
    {
        return $this->totalArea;
    }

    public function getLivingArea(): ?float
    {
        return $this->livingArea;
    }

    public function getMetersCount(): ?int
    {
        return $this->metersCount;
    }

    public function getUkProject(): ?UkProject
    {
        return $this->ukProject;
    }

    public function getBuildZid(): ?Building
    {
        return $this->buildZid;
    }

    public function getBuildId(): ?int
    {
        return $this->buildId;
    }

    public function getUkEmergencyClaimPhone(): ?string
    {
        return $this->ukEmergencyClaimPhone;
    }

    public function getClassifierUKId(): ?string
    {
        return $this->classifierUKId;
    }
}
