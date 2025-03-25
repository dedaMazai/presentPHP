<?php

namespace App\Services\PushNotifier;

use App\Services\PushNotifier\Notification\Notification;

/**
 * Interface PushNotifier
 *
 * @package App\Services\PushNotifier
 */
interface PushNotifier
{
    public function push(Notification $notification): void;
}
