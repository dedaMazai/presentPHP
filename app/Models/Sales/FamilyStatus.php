<?php

namespace App\Models\Sales;

use Spatie\Enum\Enum;

/**
 * Class FamilyStatus
 *
 * @method static self single()
 * @method static self married()
 * @method static self divorced()
 * @method static self widower()
 *
 * @package App\Models\Sales
 */
class FamilyStatus extends Enum
{
    protected static function values(): array
    {
        //TODO: fill values
        return [
            'single' => '1',
            'married' => '2',
            'divorced' => '4',
            'widower' => '8',
        ];
    }
}
