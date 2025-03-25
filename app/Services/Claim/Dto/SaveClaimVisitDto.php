<?php

namespace App\Services\Claim\Dto;

use App\Models\Claim\ClaimCatalogue\ClaimCatalogueItem;
use Carbon\Carbon;

/**
 * Class SaveClaimVisitDto
 *
 * @package App\Services\Claim\Dto
 */
class SaveClaimVisitDto
{
    /**
     * @param ClaimCatalogueItem $claimCatalogueItem
     * @param Carbon|null        $arrivalDate
     * @param string|null        $comment
     * @param ClaimImageDto[]    $imageDtos
     */
    public function __construct(
        public ClaimCatalogueItem $claimCatalogueItem,
        public ?Carbon $arrivalDate,
        public ?string $comment,
        public array $imageDtos,
    ) {
    }
}
