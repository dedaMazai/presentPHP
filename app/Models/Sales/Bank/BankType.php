<?php

namespace App\Models\Sales\Bank;

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
            'letterOfCredit' => '1',
            'mortgage' => '2',
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
