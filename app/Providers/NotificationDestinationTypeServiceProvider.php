<?php

namespace App\Providers;

use App\Services\Notification\DestinationTypeService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class NotificationDestinationTypeServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function boot(): void
    {
        $this->app->singleton(DestinationTypeService::class, function () {
            return new DestinationTypeService(config('notification_destination_types', []));
        });
    }

    public function provides(): array
    {
        return [DestinationTypeService::class];
    }
}
