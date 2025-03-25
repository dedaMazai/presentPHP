<?php

namespace App\Services\PushNotifier\Notification;

/**
 * Interface Target
 *
 * @package App\Services\PushNotifier\Notification
 */
interface Target
{
    public function getValue(): string;
}
