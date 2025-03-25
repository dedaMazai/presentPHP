<?php

namespace App\Http\Api\External\V1\Requests\Sales;

use App\Http\Api\External\V1\Requests\Request;

/**
 * Class SetMeetingRequest
 *
 * @package App\Http\Api\External\V1\Requests\Sales
 */
class SetCourierAddressRequest extends Request
{
    public function rules(): array
    {
        return [
            'id' => 'required|string',
            'city' => 'required|string',
            'address' => 'required|string',
            'description' => 'string',
        ];
    }
}
