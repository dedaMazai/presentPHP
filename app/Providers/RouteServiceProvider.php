<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\RateLimiter;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/';
    public const ADMIN_HOME = '/news';
    public const ADMIN_MARKETING_HOME = '/project-types';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    protected $namespace = 'App\\Http\\Admin\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->mapApiRoutes();
        $this->mapAdminRoutes();
        $this->mapInternalApiRoutes();
    }

    /**
     * Define the "admin" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapAdminRoutes(): void
    {
        $this->router()->group([
            'middleware' => 'admin',
            'namespace' => $this->namespace,
        ], base_path('routes/admin.php'));
    }

    protected function mapApiRoutes(): void
    {
        $this->router()->group([
            'prefix' => 'api/v1',
            'middleware' => 'api',
        ], function (Router $router) {
            require base_path('routes/api/external/api_v1.php');

            $this->mapApiVersionRoutes('v1');
        });

        $this->router()->group([
            'prefix' => 'api/v2',
            'middleware' => 'api',
        ], function (Router $router) {
            require base_path('routes/api/external/api_v2.php');

            $this->mapApiVersionRoutes('v2');
        });

        $this->router()->group([
            'prefix' => 'api/v3',
            'middleware' => 'api',
        ], function (Router $router) {
            require base_path('routes/api/external/api_v3.php');

            $this->mapApiVersionRoutes('v3');
        });
    }

    protected function mapInternalApiRoutes(): void
    {
        $this->router()->group([
            'prefix' => 'api/internal',
            'middleware' => 'internal_api',
        ], fn(Router $router) => require base_path('routes/api/internal/api.php'));
    }

    protected function mapApiVersionRoutes(string $version): void
    {
        $version = strtolower($version);
        $namespace = sprintf('App\Http\Api\External\%s\Controllers', strtoupper($version));
        $middleware = "api.$version";
        $routes = base_path("routes/api/external/api_$version.php");

        $attributes = ['prefix' => $version, 'namespace' => $namespace];
        if ($this->router()->hasMiddlewareGroup($middleware)) {
            $attributes['middleware'] = $middleware;
        }

        $this->router()->group($attributes, $routes);
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });

        RateLimiter::for('internal_api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }

    protected function router(): Router
    {
        return $this->app->make(Router::class);
    }
}
