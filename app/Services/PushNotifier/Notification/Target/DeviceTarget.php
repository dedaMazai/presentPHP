<?php

namespace App\Services\PushNotifier\Notification\Target;

use App\Services\PushNotifier\Notification\Target;

/**
 * Class DeviceTarget
 *
 * @package App\Services\PushNotifier\Notification\Target
 */
class DeviceTarget implements Target
{
    public function __construct(private string $token)
    {
    }

    public function getValue(): string
    {
        return $this->token;
    }
}
