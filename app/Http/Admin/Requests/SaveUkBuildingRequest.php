<?php

namespace App\Http\Admin\Requests;

/**
 * Class SaveUkBuildingRequest
 *
 * @package App\Http\Admin\Requests
 */
class SaveUkBuildingRequest extends Request
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'build_name' => 'required|string|max:255',
            'build_zid' => 'required|string|max:255',
            'instruction_url' => 'string',
        ];
    }
}
