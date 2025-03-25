<?php

namespace App\Http\Admin\Requests;

/**
 * Class SaveRealityTypesRequest
 *
 * @package App\Http\Admin\Requests
 */
class SaveRealityTypesRequest extends Request
{
    public function rules(): array
    {
        return [
            'reality_name' => 'required|string|max:255',
            'reality_id' => 'required|integer',
        ];
    }
}
