<?php

namespace App\Models\Pass;

use Spatie\Enum\Enum;

/**
 * Class PassType
 *
 * @method static self onetime()
 * @method static self permanent()
 *
 * @package App\Models\Pass
 */
class PassType extends Enum
{
    protected static function values(): array
    {
        return [
            'onetime' => '1',
            'permanent' => '2'
        ];
    }

    protected static function labels(): array
    {
        return [
            'onetime' => 'Разовый',
            'permanent' => 'Постоянный'
        ];
    }
}
