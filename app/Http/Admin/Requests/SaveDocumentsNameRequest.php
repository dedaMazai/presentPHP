<?php

namespace App\Http\Admin\Requests;

/**
 * Class SaveDocumentsNameRequest
 *
 * @package App\Http\Admin\Requests
 */
class SaveDocumentsNameRequest extends Request
{
    public function rules(): array
    {
        return [
            'code' => 'required|string',
            'name' => 'required|string',
            'description' => 'string',
            'object_type_code' => 'required|integer',
        ];
    }
}
