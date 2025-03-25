<?php

namespace App\Models\Claim;

use App\Models\Claim\ClaimCatalogue\ClaimCatalogueItem;

/**
 * Class ClaimService
 *
 * @package App\Models\Claim
 */
class ClaimService
{
    public function __construct(
        private string $id,
        private ClaimCatalogueItem $catalogueItem,
        private ?string $catalogueItemParentId,
        private ?string $catalogueItemParentName,
        private ?int $amount,
        private ?int $cost,
        private ?float $quantity,
        private ?int $orderNumber,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCatalogueItem(): ClaimCatalogueItem
    {
        return $this->catalogueItem;
    }

    public function getCatalogueItemParentId(): ?string
    {
        return $this->catalogueItemParentId;
    }

    public function getCatalogueItemParentName(): ?string
    {
        return $this->catalogueItemParentName;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function getCost(): ?int
    {
        return $this->cost;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function getOrderNumber(): ?int
    {
        return $this->orderNumber;
    }
}
