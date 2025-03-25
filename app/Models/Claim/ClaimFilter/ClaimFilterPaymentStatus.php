<?php

namespace App\Models\Claim\ClaimFilter;

use Spatie\Enum\Enum;

/**
 * Class ClaimFilterPaymentStatus
 *
 * @method static self fullyPaid()
 * @method static self notFullyPaid()
 *
 * @package App\Models\Claim\ClaimFilter
 */
class ClaimFilterPaymentStatus extends Enum
{
    protected static function values(): array
    {
        return [
            'fullyPaid' => 'fully_paid',
            'notFullyPaid' => 'not_fully_paid',
        ];
    }
}
