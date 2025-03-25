<?php

namespace App\Http\Api\External\V2\Controllers;

use App\Http\Traits\Responds;
use App\Models\User\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use function auth;

/**
 * Class Controller
 *
 * @package App\Http\Api\External\V1\Controllers
 */
class Controller extends BaseController
{
    use ValidatesRequests;
    use Responds;
    use AuthorizesRequests;

    private const GUARD_NAME = 'sanctum';

    protected function guard(): Guard
    {
        return auth()->guard(self::GUARD_NAME);
    }

    /**
     * @return User
     * @throws AuthenticationException
     */
    protected function getAuthUser(): User
    {
        if ($this->guard()->guest()) {
            throw new AuthenticationException();
        }
        /** @var User $user */
        $user = $this->guard()->user();

        return $user;
    }
}
