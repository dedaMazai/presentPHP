<?php

namespace App\Http\Admin\Requests;

/**
 * Class SaveInstructionRequest
 *
 * @package App\Http\Admin\Requests
 */
class SaveInstructionRequest extends Request
{
    public function rules(): array
    {
        return [
            'is_published' => 'required|boolean',
            'title' => 'required|string|max:255',
            'image_id' => 'required|integer|exists:images,id',
            'text' => 'nullable',
        ];
    }
}
