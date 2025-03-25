<?php

namespace App\Services\Claim\Dto;

/**
 * Class ClaimPassHumanItemDto
 *
 * @package App\Services\Claim\Dto
 */
class ClaimPassHumanItemDto
{
    public function __construct(
        public string $fullName,
    ) {
    }
}
