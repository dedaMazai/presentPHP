<?php

namespace App\Auth\VerificationCode\Generation;

use App\Auth\VerificationCode\VerificationCode;

/**
 * Interface VerificationCodeGenerator
 *
 * @package App\Auth\VerificationCode\Generation
 */
interface VerificationCodeGenerator
{
    public function generate(): VerificationCode;
}
