<?php

namespace App\Http\Admin\Requests;

/**
 * Class SaveFinishingRequest
 *
 * @package App\Http\Admin\Requests
 */
class SaveFinishingRequest extends Request
{
    public function rules(): array
    {
        return [
            'is_published' => 'required|boolean',
            'finishing_id' => 'required|string|max:255',
            'description' => 'string',
            'name' => 'required|string',
            'catalog_url' => 'string',
            'images' => 'array',
            'images_id' => 'array',
        ];
    }
}
