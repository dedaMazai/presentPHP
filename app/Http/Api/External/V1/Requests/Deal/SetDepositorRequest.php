<?php

namespace App\Http\Api\External\V1\Requests\Deal;

use App\Http\Api\External\V1\Requests\Request;

/**
 * Class DeleteUserRequest
 *
 * @package App\Http\Api\External\V1\Requests
 */
class SetDepositorRequest extends Request
{
    public function rules(): array
    {
        return [
            'depositor_id' => 'required|string',
            'type' => 'required|string'
        ];
    }
}
