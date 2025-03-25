<?php

namespace App\Providers;

use App\Services\Contract\ContractRepository;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\Sales\AddressRepository;
use App\Services\Sales\CharacteristicSaleRepository;
use App\Services\Sales\Property\PropertyRepository;
use Illuminate\Support\ServiceProvider;

/**
 * Class PropertyServiceProvider
 *
 * @package App\Providers
 */
class PropertyServiceProvider extends ServiceProvider
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
        $this->app->singleton(PropertyRepository::class, function () {
            return new PropertyRepository(
                $this->app->make(DynamicsCrmClient::class),
                $this->app->make(ContractRepository::class),
                $this->app->make(CharacteristicSaleRepository::class),
                $this->app->make(AddressRepository::class),
                $this->app->make('cache.store')
            );
        });
    }
}
