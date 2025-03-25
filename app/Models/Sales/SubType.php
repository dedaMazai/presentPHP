<?php

namespace App\Models\Sales;

use Spatie\Enum\Enum;

/**
 * Class PaymentMode
 *
 * @method static self free()
 * @method static self paid()
 * @method static self tradeIn()
 * @method static self premium()
 * @method static self paidOnline()
 *
 * @package App\Models\Sales
 */
class SubType extends Enum
{
    protected static function values(): array
    {
        return [
            'free' => '1',
            'paid' => '2',
            'paidOnline' => '4',
            'tradeIn' => '8',
            'premium' => '16',
        ];
    }

    protected static function labels(): array
    {
        return [
            'free' => 'Бесплатная бронь',
            'paid' => 'Платная бронь',
            'paidOnline' => 'Оплачен On-line',
            'tradeIn' => 'Трейд-ин',
            'premium' => 'Премиум',
        ];
    }
}
