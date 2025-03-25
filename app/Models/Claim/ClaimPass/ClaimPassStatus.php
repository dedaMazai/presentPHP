<?php

namespace App\Models\Claim\ClaimPass;

use Spatie\Enum\Enum;

/**
 * Class ClaimPassStatus
 *
 * @method static self cancelled()
 * @method static self accepted()
 * @method static self arrived()
 * @method static self departed()
 *
 * @package App\Models\Claim\ClaimPass
 */
class ClaimPassStatus extends Enum
{
    protected static function values(): array
    {
        return [
            'cancelled' => '0',
            'accepted' => '1',
            'arrived' => '2',
            'departed' => '3',
        ];
    }
}
