<?php

namespace App\Http\Admin\Requests;

/**
 * Class SaveUserRequest
 *
 * @package App\Http\Admin\Requests
 */
class SaveUserRequest extends Request
{
    public function rules(): array
    {
        return [
            'crm_id' => 'required',
            'phone' => 'required',
            //'manager_control' => 'required',
            'status' => 'required',
        ];
    }
}
