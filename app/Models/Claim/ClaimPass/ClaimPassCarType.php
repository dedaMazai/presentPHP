<?php

namespace App\Models\Claim\ClaimPass;

use Spatie\Enum\Enum;

/**
 * Class ClaimPassCarType
 *
 * @method static self car()
 * @method static self truck()
 * @method static self moto()
 * @method static self taxi()
 *
 * @package App\Models\Claim\ClaimPass
 */
class ClaimPassCarType extends Enum
{
    protected static function values(): array
    {
        return [
            'car' => '1',
            'truck' => '2',
            'moto' => '3',
            'taxi' => '4',
        ];
    }

    protected static function labels(): array
    {
        return [
            'car' => 'Легковой',
            'truck' => 'Грузовой',
            'moto' => 'Мотоцикл',
            'taxi' => 'Такси',
        ];
    }
}
