<?php

namespace App\Models\Sales\Demand;

use Spatie\Enum\Enum;

/**
 * Class DemandState
 *
 * @method static self open()
 * @method static self expert()
 * @method static self disqualified()
 *
 * @package App\Models\Sales\Demand
 */
class DemandState extends Enum
{
    protected static function values(): array
    {
        return [
            'open' => '0',
            'expert' => '1',
            'disqualified' => '2',
        ];
    }

    protected static function labels(): array
    {
        return [
            'open' => 'Открыта',
            'expert' => 'Квалифицированный',
            'disqualified' => 'Дисквалифицирован',
        ];
    }
}
