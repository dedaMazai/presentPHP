<?php

namespace App\Providers;

use App\Services\Apple\AppleClient;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

/**
 * Class AppleServiceProvider
 *
 * @package App\Providers
 */
class AppleServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function boot(): void
    {
        $this->app->singleton(AppleClient::class, function () {
            return new AppleClient();
        });
    }

    public function provides(): array
    {
        return [AppleClient::class];
    }
}
