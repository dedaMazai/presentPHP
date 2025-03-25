<?php

namespace App\Models\Sales;

use Spatie\Enum\Enum;

/**
 * Class PaymentMode
 *
 * @method static self full()
 * @method static self instalment()
 * @method static self mortgage()
 * @method static self subsidy()
 *
 * @package App\Models\Sales
 */
class PaymentMode extends Enum
{
    protected static function values(): array
    {
        return [
            'full' => '1',
            'instalment' => '2',
            'mortgage' => '4',
            'subsidy' => '8',
        ];
    }

    protected static function labels(): array
    {
        return [
            'full' => '100%',
            'instalment' => 'Рассрочка',
            'mortgage' => 'Ипотека',
            'subsidy' => 'Субсидия',
        ];
    }
}
