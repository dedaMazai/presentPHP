<?php

namespace App\Providers;

use App\Services\Deal\DemandDealRepository;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\Mortgage\MortgageClient;
use App\Services\Sales\Customer\CustomerRepository;
use App\Services\Sales\Deal\DealService;
use App\Services\Sales\Demand\DemandRepository;
use App\Services\Sales\Demand\DemandService;
use App\Services\Sales\MortgageApprovalRepository;
use App\Services\Sales\Property\PropertyRepository;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

/**
 * Class DemandServiceProvider
 *
 * @package App\Providers
 */
class DemandServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function boot(): void
    {
        $this->app->singleton(DemandService::class, function () {
            return new DemandService(
                config('demands.booking_limit'),
                $this->app->make(DynamicsCrmClient::class),
                $this->app->make(DealService::class),
                $this->app->make(CustomerRepository::class),
                $this->app->make(DemandDealRepository::class),
                $this->app->make(MortgageApprovalRepository::class),
                $this->app->make(MortgageClient::class),
                $this->app->make(PropertyRepository::class),
                $this->app->make(DemandRepository::class),
            );
        });
    }

    public function provides(): array
    {
        return [DemandService::class];
    }
}
