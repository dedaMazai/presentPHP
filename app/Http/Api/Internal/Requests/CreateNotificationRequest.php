<?php

namespace App\Http\Api\Internal\Requests;

use App\Models\Notification\NotificationType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class CreateNotificationRequest
 *
 * @package App\Http\Webhooks\Requests
 */
class CreateNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'text' => 'required|string',
            'type' => [
                'required',
                Rule::in(NotificationType::toValues())
            ],
            'action' => 'array',
            'action.type' => 'required_with:action|string',
            'action.payload' => 'array'
        ];
    }
}
