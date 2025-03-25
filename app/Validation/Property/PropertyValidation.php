<?php


namespace App\Validation\Property;

use App\Validation\BaseValidation;
use App\Validation\ValidationInterface;

class PropertyValidation extends BaseValidation
{
    public function validateUrl(bool $required = true): ValidationInterface
    {
        $key = 'type';

        if (!$required and !isset($this->inputs[$key])) {
            return $this;
        }

        $this->validation_rules["$key"] = ['required','string'];

        return $this;
    }
}
