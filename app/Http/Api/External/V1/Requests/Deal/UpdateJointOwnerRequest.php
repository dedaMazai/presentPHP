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
class UpdateJointOwnerRequest extends Request
{
    public function rules(): array
    {
        return [
            "last_name" => 'string',
            "first_name" => 'string',
            "middle_name" => 'string',
            "gender" => 'string',
            "phone" => 'string',
            "email" => 'string',
            "birth_date" => 'date',
            "inn" => 'string',
            "snils" => 'string',
            "married" => 'bool',
            "is_rus" => 'bool',
            "part" => 'string'
        ];
    }
}
