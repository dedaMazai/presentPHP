<?php

namespace App\Http\Admin\Requests;

/**
 * Class SaveSettingsBuildsRequest
 *
 * @package App\Http\Admin\Requests
 */
class SaveSettingsBuildsRequest extends Request
{
    public function rules(): array
    {
        return [
            'build_android_url' => 'nullable|string',
            'build_ios_url' => 'nullable|string',
        ];
    }
}
