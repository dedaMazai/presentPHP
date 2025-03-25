<?php

namespace App\Services\Claim\Dto;

use App\Models\Claim\Claim;
use Carbon\Carbon;

/**
 * Class SetClaimPaidDto
 *
 * @package App\Services\Claim\Dto
 */
class SetClaimPaidDto
{
    /**
     * @param Claim  $claim
     * @param string $paymentId
     * @param Carbon $paymentDateTime
     */
    public function __construct(
        public Claim $claim,
        public string $paymentId,
        public Carbon $paymentDateTime,
    ) {
    }
}
