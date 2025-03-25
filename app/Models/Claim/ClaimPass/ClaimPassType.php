<?php

namespace App\Models\Claim\ClaimPass;

use Spatie\Enum\Enum;

/**
 * Class ClaimPassType
 *
 * @method static self car()
 * @method static self human()
 *
 * @package App\Models\Claim\ClaimPass
 */
class ClaimPassType extends Enum
{
    protected static function values(): array
    {
        return [
            'car' => '1',
            'human' => '2',
        ];
    }

    protected static function labels(): array
    {
        return [
            'car' => 'Автомобиль',
            'human' => 'Человек',
        ];
    }
}
