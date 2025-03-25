<?php

namespace App\Models\Pass;

use Spatie\Enum\Enum;

/**
 * Class PassAssignment
 *
 * @method static self onTheCar()
 * @method static self perPerson()
 *
 * @package App\Models\Pass
 */
class PassAssignment extends Enum
{
    /**
     * @return string[]
     */
    protected static function values(): array
    {
        return [
            'onTheCar' => '1',
            'perPerson' => '2'
        ];
    }

    /**
     * @return string[]
     */
    protected static function labels()
    {
        return [
            'onTheCar' => 'На автомобиль',
            'perPerson' => 'На человека',
        ];
    }
}
