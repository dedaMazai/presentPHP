<?php

namespace App\Providers;

use App\Services\Project\ProjectAddressRepository;
use App\Services\Project\ProjectService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

/**
 * Class ProjectServiceProvider
 *
 * @package App\Providers
 */
class ProjectServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function boot(): void
    {
        $this->app->singleton(ProjectService::class, function () {
            return new ProjectService(
                $this->app->make(ProjectAddressRepository::class),
                config('services.property_feed.base_uri'),
            );
        });
    }

    public function provides(): array
    {
        return [ProjectService::class];
    }
}
