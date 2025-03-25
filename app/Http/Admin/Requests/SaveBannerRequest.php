<?php

namespace App\Http\Admin\Requests;

/**
 * Class SaveBannerRequest
 *
 * @package App\Http\Admin\Requests
 */
class SaveBannerRequest extends Request
{
    public function rules(): array
    {
        return [
            'is_published' => 'required|boolean',
            'image_id' => 'required|integer|exists:images,id',
            'news_id' => 'nullable|integer|exists:news,id',
            'category_crm_id' => 'nullable|string',
            'url' => 'nullable|max:255',
            'mode' => 'string'
        ];
    }
}
