<?php

namespace App\Models\Ad;

use App\Components\Enum\Traits\RegExable;
use Spatie\Enum\Enum;

/**
 * Class AdPlace
 *
 * @method static self ukMainTop()
 * @method static self ukMainAlert()
 * @method static self ukMarketplaceAlert()
 *
 * @package App\Models\Ad
 */
class AdPlace extends Enum
{
    use RegExable;

    protected static function values(): array
    {
        return [
            'ukMainTop' => 'uk_main_top',
            'ukMainAlert' => 'uk_main_alert',
            'ukMarketplaceAlert' => 'uk_marketplace_alert',
        ];
    }

    protected static function labels()
    {
        return [
            'ukMainTop' => 'УК. На главной - Вверху',
            'ukMainAlert' => 'УК. На главной - Алерт',
            'ukMarketplaceAlert' => 'УК. Маркетплейс - Алерт',
        ];
    }
}
