<?php

namespace App\Models\Sales;

use Spatie\Enum\Enum;

/**
 * Class AppealType
 *
 * @method static self domrf()
 * @method static self hypsber()
 *
 * @package App\Models\Sales
 */
class AppealType extends Enum
{
    protected static function values(): array
    {
        return [
            'domrf' => 'СМС код ДОМ.РФ',
            'hypsber' => 'СМС код Ипотека Сбер',
        ];
    }
}
