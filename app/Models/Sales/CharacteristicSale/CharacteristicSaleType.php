<?php

namespace App\Models\Sales\CharacteristicSale;

use Spatie\Enum\Enum;

/**
 * Class CharacteristicSaleType
 *
 * @method static self finishing()
 * @method static self discount()
 * @method static self instalments()
 * @method static self furniture()
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
            'furniture' => '64',
        ];
    }

    protected static function labels(): array
    {
        return [
            'finishing' => 'Отделка',
            'discount' => 'Скидка',
            'instalments' => 'Рассрочка',
            'furniture' => 'Мебель',
        ];
    }
}
