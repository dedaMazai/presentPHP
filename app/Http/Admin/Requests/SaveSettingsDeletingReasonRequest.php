<?php

namespace App\Http\Admin\Requests;

/**
 * Class SaveSettingsDeletingReasonRequest
 *
 * @package App\Http\Admin\Requests
 */
class SaveSettingsDeletingReasonRequest extends Request
{
    public function rules(): array
    {
        return [
            'value' => 'required|string|unique:deleting_reasons',
            'title' => 'required|string',
        ];
    }
}
