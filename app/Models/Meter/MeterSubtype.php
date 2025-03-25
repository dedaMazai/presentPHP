<?php

namespace App\Models\Meter;

use Spatie\Enum\Enum;

/**
 * Class MeterSubtype
 *
 * @method static self hot()
 * @method static self cold()
 * @method static self pure()
 *
 * @package App\Models\Meter
 */
class MeterSubtype extends Enum
{
    protected static function labels()
    {
        return [
            'hot' => 'Горячая вода',
            'cold' => 'Холодная вода',
            'pure' => 'Питьевая вода',
        ];
    }
}
