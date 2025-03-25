<?php

namespace App\Http\Api\External\V1\Requests;

/**
 * Class SaveMeterNameRequest
 *
 * @package App\Http\Api\External\V1\Requests
 */
class SaveMeterNameRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
        ];
    }
}
