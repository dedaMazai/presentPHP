<?php

namespace App\Providers;

use App\Services\Crm\CrmClient;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

/**
 * Class CrmServiceProvider
 *
 * @package App\Providers
 */
class CrmServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function boot(): void
    {
        $this->app->singleton(CrmClient::class, function () {
            return new CrmClient(config('services.crm.api_key'), config('services.crm.base_uri'));
        });
    }

    public function provides(): array
    {
        return [CrmClient::class];
    }
}
