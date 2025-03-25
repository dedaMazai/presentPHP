<?php

namespace App\Http\Admin\Requests;

use Illuminate\Support\Carbon;

/**
 * Class CreateUserRequest
 *
 * @package App\Http\Admin\Requests
 */
class CreateUserRequest extends Request
{
    public function rules(): array
    {
        return [
            'first_name' => 'required|min:2|max:255',
            'middle_name' => 'required|min:2|max:255',
            'last_name' => 'nullable|min:2|max:255',
            'phone' => 'required',
            'email' => 'required',
            'crm_id' => 'required',
            //'manager_control' => 'required',
            'birth_date' => 'required|date',
        ];
    }
}
