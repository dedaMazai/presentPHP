<?php

namespace App\Http\Admin\Requests;

/**
 * Class SaveSettingsContactRequest
 *
 * @package App\Http\Admin\Requests
 */
class SaveSettingsContactRequest extends SaveContactRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'city_id' => 'required|exists:cities,id',
        ]);
    }
}
