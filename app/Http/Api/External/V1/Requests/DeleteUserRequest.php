<?php

namespace App\Http\Api\External\V1\Requests;

/**
 * Class DeleteUserRequest
 *
 * @package App\Http\Api\External\V1\Requests
 */
class DeleteUserRequest extends Request
{
    public function rules(): array
    {
        return [
            'code' => 'required|string',
            'reason' => 'required|string',
        ];
    }
}
