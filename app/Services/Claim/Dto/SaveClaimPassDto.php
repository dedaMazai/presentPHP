<?php

namespace App\Services\Claim\Dto;

use App\Models\Claim\ClaimPass\ClaimPassType;
use Carbon\Carbon;

/**
 * Class SaveClaimPassDto
 *
 * @package App\Services\Claim\Dto
 */
class SaveClaimPassDto
{
    /**
     * @param ClaimPassType              $passType
     * @param Carbon                     $arrivalDate
     * @param string|null                $comment
     * @param ClaimPassHumanItemDto|null $humanItemDto
     * @param ClaimPassCarItemDto|null   $carItemDto
     * @param ClaimImageDto[]            $imageDtos
     */
    public function __construct(
        public ClaimPassType $passType,
        public Carbon $arrivalDate,
        public ?string $comment,
        public ?ClaimPassHumanItemDto $humanItemDto,
        public ?ClaimPassCarItemDto $carItemDto,
        public array $imageDtos,
    ) {
    }
}
