<?php

namespace App\Services\Claim\Dto;

use App\Models\Claim\ClaimPass\ClaimPassCarType;

/**
 * Class ClaimPassCarItemDto
 *
 * @package App\Services\Claim\Dto
 */
class ClaimPassCarItemDto
{
    public function __construct(
        public ClaimPassCarType $carType,
        public string $number,
    ) {
    }
}
