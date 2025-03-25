<?php

namespace App\Models\Claim;

use Spatie\Enum\Enum;

/**
 * Class ClaimPriceType
 *
 * @method static self free()
 * @method static self fixed()
 * @method static self variable()
 * @method static self agent()
 *
 * @package App\Models\Claim
 */
class ClaimPriceType extends Enum
{
    protected static function values(): array
    {
        return [
            'free' => '1',
            'fixed' => '2',
            'variable' => '3',
            'agent' => '4',
        ];
    }
}
