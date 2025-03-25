<?php

namespace App\Models\Claim\ClaimPass;

/**
 * Class ClaimPassHuman
 *
 * @package App\Models\Claim\ClaimPass
 */
class ClaimPassHuman
{
    public function __construct(
        private string $fullName,
    ) {
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }
}
