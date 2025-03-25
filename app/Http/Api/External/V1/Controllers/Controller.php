<?php

namespace App\Http\Api\External\V1\Controllers;

use App\Http\Services\Fractal\Manager;
use App\Http\Traits\Responds;
use App\Models\User\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;
use function app;
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

    protected function item(
        object|array $item,
        callable|string|object $transformer,
        ?string $type = null
    ): Response {
        return $this->response($this->serializer()->item($item, $transformer, $type));
    }

    protected function collection(
        iterable $items,
        callable|string|object $transformer,
        ?string $type = null,
        bool $hasPagination = false
    ): Response {
        return $this->response($this->serializer()->collection($items, $transformer, $type, $hasPagination));
    }

    protected function serializer(): Manager
    {
        return app(Manager::class);
    }
}
