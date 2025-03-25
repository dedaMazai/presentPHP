<?php

namespace App\Http\Admin\Requests;

use App\Models\Ad\AdPlace;
use Illuminate\Validation\Rule;

/**
 * Class SaveAdRequest
 *
 * @package App\Http\Admin\Requests
 */
class SaveAdRequest extends Request
{
    public function rules(): array
    {
        return [
            'is_published' => 'required|boolean',
            'place' => [
                'required',
                Rule::in(AdPlace::toValues()),
            ],
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image_id' => 'nullable|integer|exists:images,id',
            'news_id' => 'nullable|integer|exists:news,id',
            'url' => 'nullable|string|max:255',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'place' => AdPlace::from($this->place),
        ]);
    }
}
