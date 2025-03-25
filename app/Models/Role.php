<?php

namespace App\Models;

use Spatie\Enum\Enum;

/**
 * Class Role
 *
 * @method static self client()
 * @method static self creditor()
 * @method static self customer()
 * @method static self owner()
 * @method static self renter()
 * @method static self tenant()
 * @method static self coBorrower()
 *
 * @package App\Models
 */
class Role extends Enum
{
    protected static function values(): array
    {
        return [
            'client' => '1',
            'creditor' => '2',
            'customer' => '4',
            'owner' => '5',
            'renter' => '6',
            'tenant' => '7',
            'coBorrower' => '8',
        ];
    }

    protected static function labels(): array
    {
        return [
            'client' => 'Клиент',
            'creditor' => 'Кредитор',
            'customer' => 'Заказчик',
            'owner' => 'Собственник',
            'renter' => 'Арендатор',
            'tenant' => 'Проживающий',
            'coBorrower' => 'Созаемщик',
        ];
    }
}
