<?php

namespace App\Models\Sales;

use Spatie\Enum\Enum;

/**
 * Class ChoiceType
 *
 * @method static self select()
 * @method static self required()
 * @method static self info()
 *
 * @package App\Models\Sales
 */
class ChoiceType extends Enum
{
    protected static function values(): array
    {
        return [
            'select' => '1',
            'required' => '2',
            'info' => '3',
        ];
    }

    protected static function labels(): array
    {
        return [
            'select' => 'Значение для выбора',
            'required' => 'Обязательное значение',
            'info' => 'Информационное значение',
        ];
    }
}
