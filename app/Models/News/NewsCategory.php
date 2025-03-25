<?php

namespace App\Models\News;

use Spatie\Enum\Enum;

/**
 * Class NewsCategory
 *
 * @method static self news()
 * @method static self polls()
 * @method static self important()
 * @method static self actions()
 *
 * @package App\Models\News
 */
class NewsCategory extends Enum
{
    protected static function labels()
    {
        return [
            'news' => 'Новости',
            'polls' => 'Опросы',
            'important' => 'Важное',
            'actions' => 'Акции',
        ];
    }
}
