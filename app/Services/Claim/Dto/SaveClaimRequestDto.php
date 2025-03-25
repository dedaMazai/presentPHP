<?php

namespace App\Services\Claim\Dto;

use App\Models\Claim\ClaimCatalogue\ClaimCatalogueItem;
use Carbon\Carbon;

/**
 * Class SaveClaimRequestDto
 *
 * @package App\Services\Claim\Dto
 */
class SaveClaimRequestDto
{
    /**
     * @param ClaimCatalogueItem[] $claimCatalogueItems
     * @param string $comment
     * @param Carbon|null $arrivalDate
     * @param ClaimImageDto[]|null $imageDtos
     * @param array $theme
     */
    public function __construct(
        public array $claimCatalogueItems,
        public string $comment,
        public ?Carbon $arrivalDate,
        public ?array $imageDtos,
        public array $theme = []
    ) {
    }
}
