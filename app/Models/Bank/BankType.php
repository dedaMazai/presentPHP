<?php

namespace App\Models\Bank;

use Spatie\Enum\Enum;

/**
 * Class BankType
 *
 * @method static self letterOfCredit()
 * @method static self mortgage()
 *
 * @package App\Models\Sales
 */
class BankType extends Enum
{
    protected static function values(): array
    {
        return [
            'letterOfCredit' => 'letterofcredit',
            'mortgage' => 'hypothec',
        ];
    }

    protected static function labels(): array
    {
        return [
            'letterOfCredit' => 'Аккредитив',
            'mortgage' => 'Ипотека',
        ];
    }
}
