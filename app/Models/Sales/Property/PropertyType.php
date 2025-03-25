<?php

namespace App\Models\Sales\Property;

use Spatie\Enum\Enum;

/**
 * Class PropertyType
 *
 * @method static self living()
 * @method static self parking()
 * @method static self uninhabitable()
 * @method static self country()
 * @method static self lot()
 *
 * @package App\Models\Sales\Property
 */
class PropertyType extends Enum
{
    protected static function values(): array
    {
        return [
            'living' => '2',
            'parking' => '4',
            'uninhabitable' => '8',
            'country' => '16',
            'lot' => '32',
        ];
    }

    protected static function labels(): array
    {
        return [
            'living' => 'Жилое',
            'parking' => 'Машиноместо',
            'uninhabitable' => 'Нежилое',
            'country' => 'Загородная',
            'lot' => 'Лот',
        ];
    }
}
