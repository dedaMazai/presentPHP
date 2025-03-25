<?php

namespace App\Models\V2\Sales\Property;

use Spatie\Enum\Enum;

/**
 * Class PropertyReserveDuration
 *
 * @method static self days2()
 *
 * @package App\Models\Sales\Property
 */
class PropertyReserveDuration extends Enum
{
    protected static function values(): array
    {
        //TODO: fill values
        return [
            'day2' => '2',
        ];
    }
}
