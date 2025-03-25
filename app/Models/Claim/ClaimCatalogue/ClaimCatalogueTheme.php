<?php

namespace App\Models\Claim\ClaimCatalogue;

use Illuminate\Support\Collection;

/**
 * Class ClaimCatalogueTheme
 *
 * @package App\Models\Claim\ClaimCatalogue
 */
class ClaimCatalogueTheme
{
    /**
     * @param string                         $id
     * @param Collection<ClaimCatalogueItem> $items
     */
    public function __construct(
        private string $id,
        private Collection $items,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return Collection<ClaimCatalogueItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    /**
     * @param Collection<ClaimCatalogueItem> $items
     */
    public function setItems(Collection $items): void
    {
        $this->items = $items;
    }
}
