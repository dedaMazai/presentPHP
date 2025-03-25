<?php

namespace App\Models\V2\Sales\CharacteristicSale;

use Spatie\Enum\Enum;

/**
 * Class CharacteristicSaleType
 *
 * @method static self finishing()
 * @method static self discount()
 * @method static self instalments()
 *
 * @package App\Models\Sales\CharacteristicSale
 */
class CharacteristicSaleType extends Enum
{
    protected static function values(): array
    {
        return [
            'finishing' => '1048576',
            'discount' => '8',
            'instalments' => '16',
        ];
    }

    protected static function labels(): array
    {
        return [
            'finishing' => 'Отделка',
            'discount' => 'Скидка',
            'instalments' => 'Рассрочка',
        ];
    }
}
