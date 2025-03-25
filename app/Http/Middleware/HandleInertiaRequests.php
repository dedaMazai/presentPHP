<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

/**
 * Class HandleInertiaRequests
 *
 * @package App\Http\Middleware
 */
class HandleInertiaRequests extends Middleware
{
    /** @inheritdoc */
    protected $rootView = 'app';

    /**
     * @inheritdoc
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * @inheritdoc
     */
    public function share(Request $request)
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => fn() => $request->user()?->only('id', 'email'),
                'roles' => $request->user()?->roles()->pluck('name'),
                'permissions' => $request->user()?->getAllPermissions()->pluck('name'),
            ],
            'csrf_token' => csrf_token()
        ]);
    }
}
