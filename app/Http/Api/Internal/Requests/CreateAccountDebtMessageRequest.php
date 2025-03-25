<?php

namespace App\Http\Api\Internal\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CreateAccountDebtMessageRequest
 *
 * @package App\Http\Webhooks\Requests
 */
class CreateAccountDebtMessageRequest extends FormRequest
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
            'user_crm_ids' => 'required|array',
            'user_crm_ids.*' => 'string',
        ];
    }
}
