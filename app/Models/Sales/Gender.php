<?php

namespace App\Models\Sales;

use Spatie\Enum\Enum;

/**
 * Class Gender
 *
 * @method static self male()
 * @method static self female()
 *
 * @package App\Models\Sales
 */
class Gender extends Enum
{
    protected static function values(): array
    {
        return [
            'male' => '1',
            'female' => '2',
        ];
    }
}
