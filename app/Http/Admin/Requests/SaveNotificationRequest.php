<?php

namespace App\Http\Admin\Requests;

use App\Models\Notification\NotificationDestinationType;
use App\Models\Notification\NotificationType;
use Illuminate\Validation\Rule;

/**
 * Class SaveNotificationRequest
 *
 * @package App\Http\Admin\Requests
 */
class SaveNotificationRequest extends Request
{
    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'text' => 'required|string',
            'type' => ['required', Rule::in(NotificationType::toValues())],
            'destination' => 'required',
            'destination.type' => ['required', Rule::in(NotificationDestinationType::toValues())],
            'destination.payload' => 'sometimes|array',
            'action.type' => 'sometimes|string',
            'action.payload' => 'array'
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'type' => NotificationType::from($this->type),
            'destination.type' => NotificationDestinationType::tryFrom($this->destination['type'] ?? ''),
        ]);
    }
}
