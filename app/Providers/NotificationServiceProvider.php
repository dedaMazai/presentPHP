<?php

namespace App\Providers;

use App\Notification\NullChannel;
use App\Notification\SmsChannel;
use App\Services\Beeline\BeelineClient;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\ServiceProvider;

/**
 * Class NotificationServiceProvider
 *
 * @package App\Providers
 */
class NotificationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->bootNullChannel();
        $this->bootSmsChannel();

        $this->manager()->deliverVia('sms');
    }

    private function bootNullChannel(): void
    {
        $this->manager()->extend('null', function () {
            return new NullChannel();
        });
    }

    private function manager(): ChannelManager
    {
        return $this->app->make(ChannelManager::class);
    }

    private function bootSmsChannel(): void
    {
        $this->manager()->extend('sms', function () {
            return new SmsChannel(
                $this->manager(),
                (string)config('services.sms.driver')
            );
        });

        $this->manager()->extend('beeline', function () {
            return new BeelineClient(
                (string)config('beeline.base_uri'),
                (string)config('beeline.login'),
                (string)config('beeline.password'),
                (string)config('beeline.sender'),
            );
        });
    }
}
