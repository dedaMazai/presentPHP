<?php

namespace App\Providers;

use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\SettingsService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

/**
 * Class DynamicsCrmServiceProvider
 *
 * @package App\Providers
 */
class DynamicsCrmServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function boot(): void
    {
        $this->app->singleton(DynamicsCrmClient::class, function () {
            return new DynamicsCrmClient(
                config('services.dynamics_crm.base_uri'),
                $this->app->make(SettingsService::class)
            );
        });
    }

    public function provides(): array
    {
        return [DynamicsCrmClient::class];
    }
}
