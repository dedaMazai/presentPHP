<?php

namespace App\Http\Api\Internal\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CreateNotificationRequest
 *
 * @package App\Http\Webhooks\Requests
 */
class CreatePushNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'topics' => 'required|array',
            'users_crm_id' => 'required|array',
        ];
    }
}
