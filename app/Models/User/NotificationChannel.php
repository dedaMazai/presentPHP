<?php

namespace App\Models\User;

use App\Components\Enum\Traits\RegExable;
use Spatie\Enum\Enum;

/**
 * Class NotificationChannel
 *
 * @method static self smsNewsCompany()
 * @method static self smsNewsObject()
 * @method static self pushNewsCompany()
 * @method static self pushNewsObject()
 *
 * @package App\Models\User
 */
class NotificationChannel extends Enum
{
    use RegExable;

    protected static function values(): array
    {
        return [
            'smsNewsCompany' => 'sms_news_company',
            'smsNewsObject' => 'sms_news_object',
            'pushNewsCompany' => 'push_news_company',
            'pushNewsObject' => 'push_news_object',
        ];
    }
}
