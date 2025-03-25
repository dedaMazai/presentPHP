<?php

namespace App\Http\Admin\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

/**
 * Class Controller
 *
 * @package App\Http\Admin\Controllers
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    private const GUARD_NAME = 'admin';

    protected bool $requireLogin = true;

    public function __construct()
    {
        Auth::shouldUse(self::GUARD_NAME);

        if ($this->requireLogin) {
            $this->middleware('auth');
        }
    }
}
