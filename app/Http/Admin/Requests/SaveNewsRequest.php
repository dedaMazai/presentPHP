<?php

namespace App\Http\Admin\Requests;

use App\Models\News\NewsCategory;
use App\Models\News\NewsType;
use App\Models\Notification\NotificationDestinationType;
use Illuminate\Validation\Rule;

/**
 * Class SaveNewsRequest
 *
 * @package App\Http\Admin\Requests
 */
class SaveNewsRequest extends Request
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => [
                'required',
                Rule::in(NewsCategory::toValues()),
            ],
            'type' => [
                'required',
                Rule::in(NewsType::toValues()),
            ],
            'destination' => [
                'required',
                Rule::in(NotificationDestinationType::toValues()),
            ],
            'uk_project_id' => [
                'sometimes',
                'integer',
                'nullable',
                'exists:uk_projects,id',
            ],
            'buildings_id' => 'sometimes|array',
            'preview_image_id' => 'nullable|integer|exists:images,id',
            'tag' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:255',
            'is_published' => 'required|boolean',
            'should_send_notification' => 'bool',
            'is_sent' => 'bool',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'category' => NewsCategory::from($this->category),
            'type' => NewsType::from($this->type),
        ]);
    }
}
