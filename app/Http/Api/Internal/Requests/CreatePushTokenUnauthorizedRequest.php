<?php

namespace App\Http\Api\Internal\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CreatePushTokenUnauthorizedRequest
 *
 * @package App\Http\Webhooks\Requests
 */
class CreatePushTokenUnauthorizedRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'push_token' => 'required|string',
        ];
    }
}
