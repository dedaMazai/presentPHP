<?php

namespace App\Providers;

use App\Services\Action\ActionTypeService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class ActionServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function boot(): void
    {
        $this->app->singleton(ActionTypeService::class, function () {
            return new ActionTypeService(config('action_types', []));
        });
    }

    public function provides(): array
    {
        return [ActionTypeService::class];
    }
}
