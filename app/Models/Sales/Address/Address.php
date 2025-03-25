<?php

namespace App\Models\Sales\Address;

/**
 * Class Address
 *
 * @package App\Models\Sales\Address
 */
class Address
{
    public function __construct(
        private string $id,
        private string $name,
        private string $gkId,
        private ?string $address,
        private string $regionCode,
        private string $regionName,
        private ?string $website,
        private ?string $office,
        private ?string $latitude,
        private ?string $longitude,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getGkId(): string
    {
        return $this->gkId;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function getRegionCode(): string
    {
        return $this->regionCode;
    }

    public function getRegionName(): string
    {
        return $this->regionName;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function getOffice(): ?string
    {
        return $this->office;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }
}
