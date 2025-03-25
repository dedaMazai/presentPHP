<?php

namespace App\Validation;

/**
 * Class PhoneNumberValidator
 *
 * @package App\Validation
 */
class PhoneNumberValidator
{
    public function __invoke(mixed $attribute, mixed $value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        return (bool)preg_match('/^\+?[1-9]\d{1,14}$/', $value);
    }
}
