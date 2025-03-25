<?php

namespace App\Auth\VerificationCode;

/**
 * Class VerificationCode
 *
 * @package App\Auth\VerificationCode
 */
class VerificationCode
{
    public function __construct(
        private string $code
    ) {
    }

    public static function fromString(string $code): self
    {
        return new self($code);
    }

    public function equals(VerificationCode $other): bool
    {
        return $this->toString() === $other->toString();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    public function toString(): string
    {
        return $this->code;
    }
}
