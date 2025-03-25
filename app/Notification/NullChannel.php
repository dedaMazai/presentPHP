<?php

namespace App\Notification;

use Illuminate\Notifications\Notification;

/**
 * Class NullChannel
 *
 * @package App\Notification
 */
class NullChannel
{
    public function send(mixed $notifiable, Notification $notification): void
    {
        //
    }
}
