<?php

namespace App\Models\ContentItem;

use Spatie\Enum\Enum;

/**
 * Class ContentItemType
 *
 * @method static self text()
 * @method static self image()
 * @method static self gallery()
 * @method static self video()
 * @method static self title1lvl()
 * @method static self title2lvl()
 * @method static self title3lvl()
 * @method static self factoids()
 * @method static self numberedList()
 * @method static self unnumberedList()
 * @method static self document()
 *
 * @package App\Models\ContentItem
 */
class ContentItemType extends Enum
{
    protected static function values(): array
    {
        return [
            'text' => 'text',
            'image' => 'image',
            'gallery' => 'gallery',
            'video' => 'video',
            'title1lvl' => 'title1lvl',
            'title2lvl' => 'title2lvl',
            'title3lvl' => 'title3lvl',
            'factoids' => 'factoids',
            'numberedList' => 'numbered_list',
            'unnumberedList' => 'unnumbered_list',
            'document' => 'document'
        ];
    }

    protected static function labels()
    {
        return [
            'text' => 'Абзац текста',
            'image' => 'Изображение',
            'gallery' => 'Галерея изображений',
            'video' => 'Встроенное youtube видео',
            'title1lvl' => 'Заголовок 1-го уровня',
            'title2lvl' => 'Заголовок 2-го уровня',
            'title3lvl' => 'Заголовок 3-го уровня',
            'factoids' => 'Фактойды',
            'numberedList' => 'Нумерованный список',
            'unnumberedList' => 'Ненумерованный список',
            'document' => 'Файл'
        ];
    }
}
