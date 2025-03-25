<?php

namespace App\Http\Api\Internal\Controllers;

use App\Http\Services\Fractal\Manager;
use App\Http\Traits\Responds;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use function app;

/**
 * Class Controller
 *
 * @package App\Http\Api\Internal\Controllers
 */
class Controller extends BaseController
{
    use ValidatesRequests;
    use Responds;

    protected function serializer(): Manager
    {
        return app(Manager::class);
    }
}
