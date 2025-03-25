<?php

namespace App\Console\Commands;

use App\Services\PushNotifier\Notification\Message;
use App\Services\PushNotifier\Notification\Notification;
use App\Services\PushNotifier\Notification\Target\DeviceTarget;
use App\Services\PushNotifier\PushNotifier;
use Illuminate\Console\Command;

/**
 * Class SendTestPush
 *
 * @package App\Console\Commands
 */
class SendTestPush extends Command
{
    /**
     * @var string
     */
    protected $signature = 'push:send-test {token} {text}';

    /**
     * @var string
     */
    protected $description = 'Send push notification to device token';

    public function handle(PushNotifier $notifier): void
    {
        $token = $this->argument('token');
        $text = $this->argument('text');

        $notification = Notification::create(Message::create(title: $text), new DeviceTarget($token));
        $notifier->push($notification);

        $this->info('Push was sent');
    }
}
