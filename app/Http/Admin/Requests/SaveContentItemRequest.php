<?php

namespace App\Http\Admin\Requests;

/**
 * Class SaveContentItemRequest
 *
 * @package App\Http\Admin\Requests
 */
class SaveContentItemRequest extends Request
{
    public function rules(): array
    {
        return [
            'text' => 'string',
            'video_url' => 'string',
            'image_id' => 'integer',
            'gallery_image_ids' => 'array',
            'gallery_image_ids.*' => 'int',
            'content' => 'array',
        ];
    }
}
