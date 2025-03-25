<?php

namespace App\Http\Admin\Requests;

/**
 * Class SaveBanksRequest
 *
 * @package App\Http\Admin\Requests
 */
class SaveBanksRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'bank_id' => 'required|string|max:255',
            'image_id' => 'required|integer|exists:images,id',
        ];
    }
}
