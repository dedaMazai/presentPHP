<?php

namespace App\Http\Api\External\V1\Requests\Deal;

use App\Http\Api\External\V1\Requests\Request;
use App\Models\Role;
use App\Models\Sales\OwnerType;
use Illuminate\Validation\Rule;

/**
 * Class DeleteUserRequest
 *
 * @package App\Http\Api\External\V1\Requests
 */
class StoreJointOwnerRequest extends Request
{
    public function rules(): array
    {
        return [
            "owner_code" => [
                Rule::in(OwnerType::toValues()),
            ],
            "last_name" => 'required|string',
            "first_name" => 'required|string',
            "middle_name" => 'string',
            "gender" => 'string',
            "phone" => 'string',
            "email" => 'string',
            "birth_date" => 'required|date',
            "inn" => 'string',
            "snils" => 'string',
            "role_code" => [
                Rule::in(Role::toValues()),
            ],
            "married" => 'bool',
            "is_rus" => 'required|bool',
            "files" => 'array',
        ];
    }
}
