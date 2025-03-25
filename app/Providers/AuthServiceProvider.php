<?php

namespace App\Providers;

use App\Auth\VerificationCode\VerificationCodeManager;
use App\Http\Api\External\V1\Controllers\Auth\AuthController;
use App\Policies\AccountPolicy;
use App\Services\User\UserService;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [];

    /**
     * @return void
     */
    public function boot(): void
    {
        $this->registerPolicies();
        $this->defineGates();
        $this->registerController();
    }

    private function defineGates(): void
    {
        Gate::define('view-account', [AccountPolicy::class, 'view']);
        Gate::define('manipulate-relationship', [AccountPolicy::class, 'manipulateRelationship']);
    }

    private function registerController(): void
    {
        $this->app->singleton(AuthController::class, function () {
            return new AuthController(
                $this->app->make(VerificationCodeManager::class),
                $this->app->make(UserService::class),
                (int)config('auth.max_attempts'),
                (int)config('sanctum.expiration'),
            );
        });
    }
}
