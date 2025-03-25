<?php

namespace App\Models\V2\Contract;

use Spatie\Enum\Enum;

/**
 * Class ContractType
 *
 * @method static self account()
 *
 * @package App\Models\Contract
 */
class ContractType extends Enum
{
    protected static function values(): array
    {
        return [
            'account' => '129',
        ];
    }
}
