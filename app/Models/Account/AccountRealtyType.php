<?php

namespace App\Models\Account;

use Spatie\Enum\Enum;

/**
 * Class AccountRealtyType
 *
 * @method static self flat()
 * @method static self parking()
 * @method static self uninhabitable()
 * @method static self country()
 * @method static self lot()
 * @method static self storeroom()
 *
 * @package App\Models\Account
 */
class AccountRealtyType extends Enum
{
    protected static function values(): array
    {
        return [
            'flat' => '2',
            'parking' => '4',
            'uninhabitable' => '8',
            'country' => '16',
            'lot' => '32',
            'storeroom' => '4096',
        ];
    }

    protected static function labels()
    {
        return [
            'flat' => 'Квартира',
            'parking' => 'Машиноместо',
            'uninhabitable' => 'Нежилое',
            'country' => 'Загородная',
            'lot' => 'Лот',
            'storeroom' => 'Кладовка',
        ];
    }
}
