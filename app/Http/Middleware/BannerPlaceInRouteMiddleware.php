<?php

namespace App\Http\Middleware;

use App\Models\Banner\BannerPlace;
use Closure;

/**
 * Class BannerPlaceInRouteMiddleware
 *
 * @package App\Http\Middleware
 */
class BannerPlaceInRouteMiddleware
{
    public function handle($request, Closure $next)
    {
        $type = $request->route()->originalParameter('place');
        $request->route()->setParameter('place', BannerPlace::from($type));

        return $next($request);
    }
}
