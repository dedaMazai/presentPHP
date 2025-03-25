<?php

namespace App\Http\Admin\Requests;

/**
 * Class SaveRealityTypesRequest
 *
 * @package App\Http\Admin\Requests
 */
class SaveClientRoleTypesRequest extends Request
{
    public function rules(): array
    {
        return [
            'role_name' => 'required|string|max:255',
            'role_code' => 'required|integer',
        ];
    }
}
