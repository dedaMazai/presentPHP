<?php

namespace App\Services\Claim\Dto;

use App\Models\Claim\ClaimCatalogue\ClaimCatalogueItem;

/**
 * Class SaveClaimMarketplaceItemDto
 *
 * @package App\Services\Claim\Dto
 */
class SaveClaimMarketplaceItemDto
{
    public function __construct(
        public ClaimCatalogueItem $claimCatalogueItem,
        public int|float $count,
    ) {
    }
}
