<?php

namespace App\Services\Claim\Dto;

use App\Models\Claim\ClaimCatalogue\ClaimCatalogueItem;

/**
 * Class SaveClaimAppealDto
 *
 * @package App\Services\Claim\Dto
 */
class SaveClaimAppealDto
{
    /**
     * @param ClaimCatalogueItem[] $claimCatalogueItems
     * @param string               $comment
     * @param ClaimImageDto[]      $imageDtos
     */
    public function __construct(
        public array $claimCatalogueItems,
        public string $comment,
        public array $imageDtos,
    ) {
    }
}
