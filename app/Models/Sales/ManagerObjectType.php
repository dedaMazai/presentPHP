<?php

namespace App\Models\Sales;

use Spatie\Enum\Enum;

/**
 * Class ManagerObjectType
 *
 * @method static self demand()
 * @method static self contract()
 *
 * @package App\Models\Sales
 */
class ManagerObjectType extends Enum
{
    protected static function values(): array
    {
        return [
            'demand' => 'demand',
            'contract' => 'contract',
        ];
    }
}
