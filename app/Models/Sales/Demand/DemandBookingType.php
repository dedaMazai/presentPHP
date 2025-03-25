<?php

namespace App\Models\Sales\Demand;

use Spatie\Enum\Enum;

/**
 * Class DemandBookingType
 *
 * @method static self free()
 * @method static self paid()
 * @method static self paidMortgage()
 * @method static self tradeIn()
 * @method static self premium()
 *
 * @package App\Models\Sales\Demand
 */
class DemandBookingType extends Enum
{
    protected static function values(): array
    {
        return [
            'free' => '1',
            'paid' => '2',
            'paidMortgage' => '4',
            'tradeIn' => '8',
            'premium' => '16',
        ];
    }

    protected static function labels(): array
    {
        return [
            'free' => 'Бесплатная бронь',
            'paid' => 'Платная бронь',
            'paidMortgage' => 'Платная бронь (Ипотека)',
            'tradeIn' => 'Трейд-ин',
            'premium' => 'Премиум',
        ];
    }
}
