<?php

namespace App\Http\Api\External\V1\Requests\Sales;

use App\Http\Api\External\V1\Requests\Request;

/**
 * Class SetMeetingRequest
 *
 * @package App\Http\Api\External\V1\Requests\Sales
 */
class SetMeetingRequest extends Request
{
    public function rules(): array
    {
        return [
            'owners' => 'array',
            'owners.*.joint_owner_id' => 'required|string',
            'owners.*.address' => 'required|string',
            'owners.*.phone' => 'required|string',
        ];
    }
}
