<?php

namespace App\Services\Claim\Dto;

use Carbon\Carbon;

/**
 * Class SaveClaimMarketplaceDto
 *
 * @package App\Services\Claim\Dto
 */
class SaveClaimMarketplaceDto
{
    /**
     * @param SaveClaimMarketplaceItemDto[] $claimMarketplaceItemDtos
     * @param Carbon|null                   $arrivalDate
     * @param ClaimImageDto[]               $imageDtos
     */
    public function __construct(
        public array $claimMarketplaceItemDtos,
        public ?Carbon $arrivalDate,
        public array $imageDtos,
        public ?string $comment,
    ) {
    }
}
