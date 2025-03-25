<?php

namespace App\Http\Admin\Requests;

/**
 * Class SaveUkProjectRequest
 *
 * @package App\Http\Admin\Requests
 */
class SaveUkProjectRequest extends Request
{
    public function rules(): array
    {
        return [
            'is_published' => 'required|boolean',
            'name' => 'required|string|max:255',
            'crm_1c_id' => 'required|string|max:255',
            'image_id' => 'required|integer|exists:images,id',
            'market_image_id' => 'nullable|integer|exists:images,id',
            'description' => 'required|string',
            'postcode' => 'required|string|max:255',
            'uk_emergency_claim_phone' => 'required|string|max:255',
        ];
    }
}
