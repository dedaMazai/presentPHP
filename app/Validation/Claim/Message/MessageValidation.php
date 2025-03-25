<?php


namespace App\Validation\Claim\Catalogue;

use App\Validation\BaseValidation;
use App\Validation\ValidationInterface;

class MessageValidation extends BaseValidation
{
    public function validateText(bool $required = true): ValidationInterface
    {
        $key = 'text';

        if (!$required and !isset($this->inputs[$key])) {
            return $this;
        }

        $this->validation_rules["$key"] = ['required','string'];

        return $this;
    }
}
