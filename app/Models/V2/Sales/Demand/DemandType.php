<?php

namespace App\Models\V2\Sales\Demand;

use Spatie\Enum\Enum;

/**
 * Class DemandType
 *
 * @method static self cash()
 * @method static self mortgage()
 * @method static self options()
 *
 * @package App\Models\Sales\Demand
 */
class DemandType extends Enum
{
    protected static function values(): array
    {
        return [
            'cash' => '1',
            'mortgage' => '16',
        ];
    }

    protected static function labels(): array
    {
        return [
            'cash' => 'Купить',
            'mortgage' => 'Ипотека',
        ];
    }
}
