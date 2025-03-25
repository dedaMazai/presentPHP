<?php

namespace App\Auth\VerificationCode\Generation;

use App\Auth\VerificationCode\VerificationCode;
use Assert\Assert;

/**
 * Class FixedVerificationCodeGenerator
 *
 * @package App\Auth\VerificationCode\Generation
 */
class FixedVerificationCodeGenerator implements VerificationCodeGenerator
{
    public function __construct(
        private string $code
    ) {
        Assert::that($code)->notEmpty();
    }

    public function generate(): VerificationCode
    {
        return VerificationCode::fromString($this->code);
    }
}
