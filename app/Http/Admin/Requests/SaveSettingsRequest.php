<?php

namespace App\Http\Admin\Requests;

/**
 * Class SaveSettingsRequest
 *
 * @package App\Http\Admin\Requests
 */
class SaveSettingsRequest extends Request
{
    public function rules(): array
    {
        return [
            'main_office_title' => 'string',
            'main_office_address' => 'required|string',
            'main_office_phone' => 'required|string',
            'main_office_email' => 'required|string',
            'main_office_lat' => 'required|numeric',
            'main_office_long' => 'required|numeric',
            'phone' => 'required|string',
        ];
    }
}
