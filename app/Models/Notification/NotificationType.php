<?php

namespace App\Models\Notification;

use App\Components\Enum\Traits\RegExable;
use Spatie\Enum\Enum;

/**
 * Class NotificationType
 *
 * @method static self marketingActivity()
 * @method static self purchaseProcess()
 * @method static self uk()
 * @method static self news()
 * @method static self claim()
 *
 * @package App\Models\Notification
 */
class NotificationType extends Enum
{
    use RegExable;

    protected static function values(): array
    {
        return [
            'marketingActivity' => 'marketing_activity',
            'purchaseProcess' => 'purchase_process',
            'uk' => 'uk',
            'news' => 'news',
            'claim' => 'claim'
        ];
    }

    protected static function labels(): array
    {
        return [
            'marketingActivity' => 'Маркетинговая активность',
            'purchaseProcess' => 'Процесс покупки',
            'uk' => 'Обслуживание в УК',
            'news' => 'Новости',
            'claim' => 'Заявки'
        ];
    }

    /**
     * @return self[]
     */
    public static function clientTypes(): array
    {
        return [
            self::marketingActivity(),
            self::purchaseProcess(),
            self::uk(),
            self::news(),
            self::claim(),
        ];
    }

    /**
     * @return string[]
     * @psalm-return array<string|int, string>
     */
    public static function clientTypesToArray(): array
    {
        $array = [];

        foreach (static::clientTypes() as $definition) {
            $array[$definition->value] = $definition->label;
        }

        return $array;
    }
}
