<?php

namespace App\Services\PushNotifier\Notification\Target;

use App\Services\PushNotifier\Notification\Target;

/**
 * Class ChannelTarget
 *
 * @package App\Services\PushNotifier\Notification\Target
 */
class ChannelTarget implements Target
{
    public function __construct(private string $channel)
    {
    }

    public function getValue(): string
    {
        return $this->channel;
    }
}
