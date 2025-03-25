<?php

namespace App\Http\Api\External\V1\Requests;

use App\Auth\VerificationCode\VerificationCase;
use Illuminate\Validation\Rule;

/**
 * Class SendVerificationCodeRequest
 *
 * @package App\Http\Api\External\V1\Requests
 */
class SendVerificationCodeRequest extends Request
{
    public function rules(): array
    {
        $phoneValidationRules = ['required', 'phone_number'];
        if ($this->case?->equals(VerificationCase::registration())) {
            $phoneValidationRules[] = 'unique:users,phone,NULL,NULL,deleted_at,NULL';
        }

        return [
            'phone' => $phoneValidationRules,
            'case' => ['required', Rule::in(VerificationCase::toValues())],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'case' => VerificationCase::tryFrom($this->case),
        ]);
    }
}
