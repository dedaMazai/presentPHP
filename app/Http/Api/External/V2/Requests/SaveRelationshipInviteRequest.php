<?php

namespace App\Http\Api\External\V2\Requests;

/**
 * Class SaveRelationshipInviteRequest
 *
 * @package App\Http\Api\External\V1\Requests
 */
class SaveRelationshipInviteRequest extends Request
{
    public function rules(): array
    {
        return [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone' => 'required|phone_number',
            'birth_date' => 'required|date',
            'role' => 'required|string',
        ];
    }
}
