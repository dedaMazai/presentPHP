<?php

namespace App\Http\Api\External\V1\Traits;

use App\Auth\VerificationCode\VerificationCase;
use App\Auth\VerificationCode\VerificationCode;
use App\Auth\VerificationCode\WrongVerificationCodeException;
use Illuminate\Validation\ValidationException;

trait VerifyCode
{
    /**
     * @param VerificationCase $case
     * @param string           $phone
     * @param string           $code
     *
     * @throws ValidationException
     */
    private function verifyCode(VerificationCase $case, string $phone, string $code): void
    {
        try {
            $this->verificationCodeManager->verify($case, $phone, VerificationCode::fromString($code));
        } catch (WrongVerificationCodeException) {
            throw ValidationException::withMessages([
                'verification_code' => 'These credentials do not match our records.',
            ]);
        }
    }
}
