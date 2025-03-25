<?php

namespace App\Providers;

use App\Services\Crm\CrmClient;
use App\Services\Meter\MeterEnterPeriodRepository;
use Illuminate\Support\ServiceProvider;

/**
 * Class MeterEnterPeriodServiceProvider
 *
 * @package App\Providers
 */
class MeterEnterPeriodServiceProvider extends ServiceProvider
{
    /**
     * @inheritDoc
     */
    public function register()
    {
        $this->registerRepositories();
    }

    private function registerRepositories(): void
    {
        $this->app->singleton(MeterEnterPeriodRepository::class, function () {
            return new MeterEnterPeriodRepository(
                $this->app->make(CrmClient::class),
                $this->app->make('cache.store')
            );
        });
    }
}
