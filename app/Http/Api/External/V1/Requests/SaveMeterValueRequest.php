<?php

namespace App\Http\Api\External\V1\Requests;

/**
 * Class SaveMeterValueRequest
 *
 * @package App\Http\Api\External\V1\Requests
 */
class SaveMeterValueRequest extends Request
{
    public function rules(): array
    {
        return [
            'id' => 'required|string|max:255',
            'values.*.tariff_id' => 'required|string|max:255',
            'values.*.current_value' => 'nullable|numeric',
        ];
    }
}
