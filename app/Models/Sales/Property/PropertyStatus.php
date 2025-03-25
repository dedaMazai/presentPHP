<?php

namespace App\Models\Sales\Property;

use Spatie\Enum\Enum;

/**
 * Class PropertyStatus
 *
 * @method static self assessment()
 * @method static self returned()
 * @method static self free()
 * @method static self oralBooking()
 * @method static self reserve()
 * @method static self paidBooking()
 * @method static self rent()
 * @method static self sale()
 * @method static self sold()
 * @method static self externalSale()
 *
 * @package App\Models\Sales\Property
 */
class PropertyStatus extends Enum
{
    protected static function values(): array
    {
        return [
            'assessment' => '1',
            'returned' => '2',
            'free' => '4',
            'oralBooking' => '8',
            'reserve' => '16',
            'paidBooking' => '32',
            'rent' => '64',
            'sale' => '128',
            'sold' => '1024',
            'externalSale' => '2048',
        ];
    }

    protected static function labels(): array
    {
        return [
            'assessment' => 'Оценка',
            'returned' => 'Возвращено',
            'free' => 'Свободно',
            'oralBooking' => 'Ус.Бронь',
            'reserve' => 'Стр.Резерв',
            'paidBooking' => 'Пл.Бронь',
            'rent' => 'Аренда',
            'sale' => 'Продажа',
            'sold' => 'Реализовано',
            'externalSale' => 'Внешняя продажа',
        ];
    }
}
