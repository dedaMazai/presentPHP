<?php

namespace App\Http\Middleware;

use App\Models\Admin\Admin;
use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param Request     $request
     * @param Closure     $next
     * @param string|null ...$guards
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return $this->redirectHome();
            }
        }

        return $next($request);
    }

    private function redirectHome(): Redirector|Application|RedirectResponse
    {
        if (Auth::guard('admin')->check()) {
            if (Auth::user()->hasRole([Admin::ROLE_ADMIN, Admin::ROLE_UK])) {
                return redirect(RouteServiceProvider::ADMIN_HOME);
            } else {
                return redirect(RouteServiceProvider::ADMIN_MARKETING_HOME);
            }
        }

        return redirect(RouteServiceProvider::HOME);
    }
}
