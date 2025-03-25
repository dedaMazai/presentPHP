<?php

namespace App\Notification;

use BadMethodCallException;
use Illuminate\Notifications\Notification;

/**
 * Class SmsNotificationAdapter
 *
 * @package App\Notification
 */
class SmsNotificationAdapter extends Notification
{
    public function __construct(
        private Notification $original
    ) {
    }

    public function __call(string $name, array $arguments)
    {
        if (!str_starts_with($name, 'to')) {
            throw new BadMethodCallException();
        }

        return $this->original->toSms(...$arguments);
    }
}
