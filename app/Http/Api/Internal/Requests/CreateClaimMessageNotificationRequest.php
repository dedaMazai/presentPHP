<?php

namespace App\Http\Api\Internal\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CreateNotificationRequest
 *
 * @package App\Http\Webhooks\Requests
 */
class CreateClaimMessageNotificationRequest extends FormRequest
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
            'account_number' => 'required|string',
        ];
    }
}
