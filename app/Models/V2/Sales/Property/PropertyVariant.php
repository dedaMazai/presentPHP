<?php

namespace App\Models\V2\Sales\Property;

use Spatie\Enum\Enum;

/**
 * Class PropertyVariant
 *
 * @method static self flatWithFinishing()
 * @method static self apartmentWithFurniture()
 * @method static self apartmentWithoutFurniture()
 * @method static self parking()
 * @method static self parkingUnderground()
 * @method static self flatWithoutFinishing()
 * @method static self commerce()
 * @method static self whiteBox()
 * @method static self officeWithFinishing()
 * @method static self officeWithoutFinishing()
 * @method static self fitness()
 * @method static self restaurant()
 * @method static self carWash()
 * @method static self retail()
 * @method static self uninhabitable()
 * @method static self studio()
 * @method static self pantry()
 * @method static self social()
 * @method static self propertyFinishing()
 *
 * @package App\Models\Sales\Property
 */
class PropertyVariant extends Enum
{
    protected static function values(): array
    {
        return [
            'flatWithFinishing' => '1',
            'apartmentWithFurniture' => '2',
            'apartmentWithoutFurniture' => '3',
            'parking' => '4',
            'parkingUnderground' => '8',
            'flatWithoutFinishing' => '11',
            'whiteBox' => '13',
            'commerce' => '16',
            'officeWithFinishing' => '32',
            'officeWithoutFinishing' => '34',
            'fitness' => '64',
            'restaurant' => '128',
            'carWash' => '256',
            'retail' => '512',
            'uninhabitable' => '1024',
            'studio' => '2048',
            'pantry' => '4096',
            'social' => '8192',
            'propertyFinishing' => '16384',
        ];
    }

    protected static function labels(): array
    {
        return [
            'flatWithFinishing' => 'Квартиры с отделкой',
            'apartmentWithFurniture' => 'Апартаменты с мебелью',
            'apartmentWithoutFurniture' => 'Апартаменты без мебели',
            'parking' => 'Паркинги (надземные)',
            'parkingUnderground' => 'Паркинги (подземные)',
            'flatWithoutFinishing' => 'Квартиры без отделки',
            'whiteBox' => 'White Box',
            'commerce' => 'Торговля',
            'officeWithFinishing' => 'Офисы с отделкой',
            'officeWithoutFinishing' => 'Офисы без отделки',
            'fitness' => 'Фитнес',
            'restaurant' => 'Ресторан',
            'carWash' => 'Автомойка',
            'retail' => 'Сетевой ритейл',
            'uninhabitable' => 'Нежилые',
            'studio' => 'Студия',
            'pantry' => 'Кладовое помещение',
            'social' => 'Соц. объект',
            'propertyFinishing' => 'Отделка жилой недвижимости',
        ];
    }
}
