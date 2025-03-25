<?php

namespace App\Notification;

use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Notifications\Notification;

/**
 * Class SmsChannel
 *
 * @package App\Notification
 */
class SmsChannel
{
    public function __construct(
        private ChannelManager $channelManager,
        private string $driver
    ) {
    }

    public function send(mixed $notifiable, Notification $notification): void
    {
        if (!$notifiable->routeNotificationFor('sms')) {
            return;
        }

        $this->channelManager->driver($this->driver)->send(
            $this->adaptNotifiable($notifiable),
            $this->adaptNotification($notification)
        );
    }

    private function adaptNotifiable(object $original): AnonymousNotifiable
    {
        return (new AnonymousNotifiable())->route(
            $this->driver,
            $original->routeNotificationFor('sms')
        );
    }

    private function adaptNotification(Notification $original): Notification
    {
        return new SmsNotificationAdapter($original);
    }
}
