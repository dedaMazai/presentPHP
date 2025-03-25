<?php

namespace App\Http\Api\External\V1\Requests\Auth;

use App\Http\Api\External\V1\Requests\Request;
use Illuminate\Validation\Rule;

/**
 * Class RegisterRequest
 *
 * @package App\Http\Api\External\V1\Requests\Auth
 */
class RegisterRequest extends Request
{
    public function rules(): array
    {
        return [
//            'phone' => ['required', 'phone_number', Rule::unique('users')->whereNull('deleted_at')],
            'phone' => ['required', 'phone_number'],
            'first_name' => 'required|string|min:2|max:255',
            'last_name' => 'required|string|min:2|max:255',
            'middle_name' => 'nullable|string|min:2|max:255',
            'birth_date' => 'required|date',
            'email' => 'required|email|max:255',
            'case' => 'required|string',
            'middleName' => 'min:2|max:255',
        ];
    }
}
