<?php

namespace App\Models\Project;

use Spatie\Enum\Enum;

/**
 * Class ProjectPropertyType
 *
 * @method static self flat()
 * @method static self pantry()
 * @method static self parking()
 * @method static self streetRetail()
 * @method static self apartment()
 * @method static self office()
 * @method static self retailSpace()
 * @method static self shoppingCenter()
 *
 * @package App\Models\Project
 */
class ProjectPropertyType extends Enum
{
    protected static function values(): array
    {
        return [
            'flat' => 'flat',
            'pantry' => 'pantry',
            'parking' => 'parking',
            'streetRetail' => 'street_retail',
            'apartment' => 'apartment',
            'office' => 'office',
            'retailSpace' => 'retail_space',
            'shoppingCenter' => 'shopping_center',
        ];
    }

    protected static function labels()
    {
        return [
            'flat' => 'Квартиры',
            'pantry' => 'Кладовки',
            'parking' => 'Паркинг',
            'streetRetail' => 'Стрит ритейл',
            'apartment' => 'Апартаменты',
            'office' => 'Офисы',
            'retailSpace' => 'Торговая галерея',
            'shoppingCenter' => 'Торговый центр',
        ];
    }
}
