<?php

namespace App\Http\Admin\Requests;

/**
 * Class SaveSupportTopicsRequest
 *
 * @package App\Http\Admin\Requests
 */
class SaveSupportTopicsRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'is_published' => 'required|boolean',
        ];
    }
}
