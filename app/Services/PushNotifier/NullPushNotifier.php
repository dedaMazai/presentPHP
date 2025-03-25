<?php

namespace App\Services\PushNotifier;

use App\Services\PushNotifier\Notification\Notification;

/**
 * Class NullPushNotifier
 *
 * @package App\Services\PushNotifier
 */
final class NullPushNotifier implements PushNotifier
{
    public function push(Notification $notification): void
    {
        // do nothing
    }
}
