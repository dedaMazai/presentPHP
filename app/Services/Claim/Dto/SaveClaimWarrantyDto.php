<?php

namespace App\Services\Claim\Dto;

use App\Models\Claim\ClaimCatalogue\ClaimCatalogueItem;
use Carbon\Carbon;

/**
 * Class SaveClaimWarrantyDto
 *
 * @package App\Services\Claim\Dto
 */
class SaveClaimWarrantyDto
{
    /**
     * @param ClaimCatalogueItem[] $claimCatalogueItems
     * @param string               $comment
     * @param Carbon|null          $arrivalDate
     * @param ClaimImageDto[]      $imageDtos
     */
    public function __construct(
        public array $claimCatalogueItems,
        public string $comment,
        public ?Carbon $arrivalDate,
        public array $imageDtos,
    ) {
    }
}
