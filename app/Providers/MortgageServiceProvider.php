<?php

namespace App\Providers;

use App\Services\Mortgage\MortgageClient;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class MortgageServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function boot(): void
    {
        $this->app->singleton(MortgageClient::class, function () {
            return new MortgageClient(config('services.mortgage.base_uri'));
        });
    }

    public function provides(): array
    {
        return [MortgageClient::class];
    }
}
