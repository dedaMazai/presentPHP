<?php

namespace App\Http\Api\Internal\Controllers;

use App;
use App\Http\Api\Internal\Requests\CreatePushTokenUnauthorizedRequest;
use App\Models\UnauthorizedPushtokens;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PushTokenUnauthorizedController
 *
 * @package App\Http\Api\Internal\Controllers
 */
class PushTokenUnauthorizedController extends Controller
{
    public function __construct()
    {
    }

    public function createPushTokenUnauthorized(CreatePushTokenUnauthorizedRequest $request): Response
    {
        $push_token = $request->input('push_token');

        if (!empty($push_token)) {
            UnauthorizedPushtokens::create($request->validated());
        }

        return $this->empty();
    }
}
