<?php

namespace App\Models\Sales;

use Spatie\Enum\Enum;

/**
 * Class OwnerType
 *
 * @method static self joint()
 * @method static self shared()
 * @method static self sharedPrenuptial()
 * @method static self representative()
 * @method static self personal()
 *
 * @package App\Models\Sales
 */
class OwnerType extends Enum
{
    protected static function values(): array
    {
        return [
            'minusJoint' => '-1',
            'joint' => '1',
            'shared' => '2',
            'sharedPrenuptial' => '3',
            'representative' => '4',
            'personal' => '5',
        ];
    }

    protected static function labels(): array
    {
        return [
            'minusJoint' => '-1',
            'joint' => 'Совместная собственность (нет брачного договора)',
            'shared' => 'Долевая собственность',
            'sharedPrenuptial' => 'Долевая собственность с брачным договором',
            'representative' => 'Представитель (не участвует в сделке)',
            'personal' => 'Индивидуальная собственность',
        ];
    }
}
