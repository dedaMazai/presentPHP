<?php

namespace App\Models\Pass;

use Spatie\Enum\Enum;

/**
 * Class PassAssignment
 *
 * @method static self passenger()
 * @method static self cargo()
 * @method static self bike()
 * @method static self taxi()
 *
 * @package App\Models\Pass
 */
class PassCarType extends Enum
{
    /**
     * @return string[]
     */
    protected static function values(): array
    {
        return [
            'passenger' => '1',
            'cargo' => '2',
            'bike' => '3',
            'taxi' => '4',
        ];
    }

    /**
     * @return string[]
     */
    protected static function labels()
    {
        return [
            'passenger' => 'Легковой',
            'cargo' => 'Грузовой',
            'bike' => 'Мотоцикл',
            'taxi' => 'Такси',
        ];
    }
}
