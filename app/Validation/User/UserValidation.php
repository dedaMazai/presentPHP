<?php


namespace App\Validation\User;

use App\Validation\BaseValidation;
use App\Validation\ValidationInterface;

class UserValidation extends BaseValidation
{
    public function validateEmail(bool $required = true): ValidationInterface
    {
        $key = 'email';

        if (!$required and !isset($this->inputs[$key])) {
            return $this;
        }

        $this->validation_rules["$key"] = ['required','email','max:255'];

        return $this;
    }

    public function validateToken(bool $required = true): ValidationInterface
    {
        $key = 'token';

        if (!$required and !isset($this->inputs[$key])) {
            return $this;
        }

        $this->validation_rules["$key"] = ['required','string','max:255'];

        return $this;
    }
}
