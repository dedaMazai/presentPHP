<?php

namespace App\Models\Sales\Property;

use Spatie\Enum\Enum;

/**
 * Class PropertyReserveType
 *
 * @method static self days()
 *
 * @package App\Models\Sales\Property
 */
class PropertyReserveType extends Enum
{
    protected static function values(): array
    {
        //TODO: fill values
        return [
            'hours' => '1',
            'days' => '2',
        ];
    }
}
