<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpFoundation\Request;

class InternalApiAuthenticate
{
    /**
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     * @throws AuthenticationException
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $accessKey = config('internal_api.access_key');
        if (!$accessKey || $accessKey !== $request->headers->get('X-Access-Key')) {
            throw new AuthenticationException();
        }

        return $next($request);
    }
}
