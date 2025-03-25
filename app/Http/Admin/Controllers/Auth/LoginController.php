<?php

namespace App\Http\Admin\Controllers\Auth;

use App\Http\Admin\Controllers\Controller;
use App\Models\Admin\Admin;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

/**
 * Class LoginController
 *
 * @package App\Http\Admin\Controllers
 */
class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected bool $requireLogin = false;

    public function showLoginForm(): Response
    {
        return inertia('Auth/LoginForm');
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();

        return redirect(route('login'));
    }

    public function redirectTo(): string
    {
        if (Auth::user()->hasRole([Admin::ROLE_ADMIN, Admin::ROLE_UK])) {
            return RouteServiceProvider::ADMIN_HOME;
        } else {
            return RouteServiceProvider::ADMIN_MARKETING_HOME;
        }
    }
}
