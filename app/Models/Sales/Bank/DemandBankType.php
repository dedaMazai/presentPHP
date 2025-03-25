<?php

namespace App\Models\Sales\Bank;

use Spatie\Enum\Enum;

/**
 * Class DemandBankType
 *
 * @method static self letterofcredit()
 * @method static self hypothec()
 *
 * @package App\Models\Sales\Bank
 */
class DemandBankType extends Enum
{
    protected static function values(): array
    {
        return [
            'letterofcredit' => 'LetterOfCreditBank',
            'hypothec' => 'HypothecBank',
        ];
    }

    protected static function labels(): array
    {
        return [
            'letterofcredit' => 'Аккредитив',
            'hypothec' => 'Ипотека',
        ];
    }
}
