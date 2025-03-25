<?php

namespace App\Models\News;

use Spatie\Enum\Enum;

/**
 * Class NewsType
 *
 * @method static self pioneer()
 * @method static self projects()
 * @method static self uk()
 * @method static self general()
 *
 * @package App\Models\News
 */
class NewsType extends Enum
{
    protected static function labels()
    {
        return [
            'pioneer' => 'ГК Пионер',
            'projects' => 'Проекты',
            'uk' => 'УК',
            'general' => 'Общие',
        ];
    }

    public function isCommon(): bool
    {
        return $this->equals(self::pioneer(), self::projects(), self::general());
    }
}
