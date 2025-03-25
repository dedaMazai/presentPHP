<?php

namespace App\Models\Pass;

use Spatie\Enum\Enum;

/**
 * Class PassStatus
 *
 * @method static self cancelled()
 * @method static self taken()
 * @method static self arrived()
 * @method static self departed()
 *
 * @package App\Models\Pass
 */
class PassStatus extends Enum
{
    protected static function values(): array
    {
        return [
            'cancelled' => '0',
            'taken' => '1',
            'arrived' => '2',
            'departed' => '3',
        ];
    }

    protected static function labels()
    {
        return [
            'cancelled' => 'Отменен',
            'taken' => 'Принят',
            'arrived' => 'Прибыл',
            'departed' => 'Отбыл'
        ];
    }
}
