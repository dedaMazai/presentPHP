<?php

namespace App\Models\Banner;

use App\Components\Enum\Traits\RegExable;
use Spatie\Enum\Enum;

/**
 * Class BannerPlace
 *
 * @method static self smallMain()
 * @method static self galleryMarketplace()
 *
 * @package App\Models\Banner
 */
class BannerPlace extends Enum
{
    use RegExable;

    protected static function values(): array
    {
        return [
            'smallMain' => 'small_main',
            'galleryMarketplace' => 'gallery_marketplace',
        ];
    }

    protected static function labels()
    {
        return [
            'smallMain' => 'Маленький на главной',
            'galleryMarketplace' => 'Галерея в маркетплейсе',
        ];
    }
}
