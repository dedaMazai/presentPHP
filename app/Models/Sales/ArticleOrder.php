<?php

namespace App\Models\Sales;

/**
 * Class ArticleOrder
 *
 * @package App\Models\Sales
 */
class ArticleOrder
{
    public function __construct(
        private string $id,
        private string $name,
        private ?string $additionalOptionId,
        private ?float $quantity,
        private ?string $unitCode,
        private ?string $unitName,
        private ?float $cost,
        private ?float $price,
        private ?float $sum,
        private ?string $serviceId,
        private ?string $propertyId,
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

    public function getAdditionalOptionId(): ?string
    {
        return $this->additionalOptionId;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function getUnitCode(): ?string
    {
        return $this->unitCode;
    }

    public function getUnitName(): ?string
    {
        return $this->unitName;
    }

    public function getCost(): ?float
    {
        return $this->cost;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function getSum(): ?float
    {
        return $this->sum;
    }

    public function getServiceId(): ?string
    {
        return $this->serviceId;
    }

    public function getPropertyId(): ?string
    {
        return $this->propertyId;
    }
}
