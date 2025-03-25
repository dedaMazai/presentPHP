<?php

namespace App\Auth\VerificationCode\Generation;

use App\Auth\VerificationCode\VerificationCode;
use Assert\Assert;

/**
 * Class NumericalVerificationCodeGenerator
 *
 * @package App\Auth\VerificationCode\Generation
 */
class NumericalVerificationCodeGenerator implements VerificationCodeGenerator
{
    public function __construct(
        private int $length
    ) {
        Assert::that($length)
            ->greaterThan(0)
            ->lessThan(10);
    }

    public function generate(): VerificationCode
    {
        return VerificationCode::fromString($this->getRandomNumber());
    }

    private function getRandomNumber(): int
    {
        $min = str_pad('1', $this->length, '0');
        $max = str_pad('9', $this->length, '9');

        return random_int($min, $max);
    }
}
