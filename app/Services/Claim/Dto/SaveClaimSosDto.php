<?php

namespace App\Services\Claim\Dto;

use App\Models\Claim\ClaimCatalogue\ClaimCatalogueItem;

/**
 * Class SaveClaimSosDto
 *
 * @package App\Services\Claim\Dto
 */
class SaveClaimSosDto
{
    /**
     * @param ClaimCatalogueItem $claimCatalogueItem
     * @param string             $comment
     * @param ClaimImageDto[]    $imageDtos
     */
    public function __construct(
        public ClaimCatalogueItem $claimCatalogueItem,
        public string $comment,
        public array $imageDtos,
    ) {
    }
}
