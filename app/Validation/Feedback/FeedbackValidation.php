<?php

namespace App\Validation\Feedback;

use App\Validation\BaseValidation;
use App\Validation\ValidationInterface;

class FeedbackValidation extends BaseValidation
{
    public function validateMessage(bool $required = true): ValidationInterface
    {
        $key = 'message';

        if (!$required and !isset($this->inputs[$key])) {
            return $this;
        }

        $this->validation_rules["$key"] = ['required','string'];

        return $this;
    }

    public function validatePhone(bool $required = true): ValidationInterface
    {
        $key = 'phone';

        if (!$required and !isset($this->inputs[$key])) {
            return $this;
        }

        $this->validation_rules["$key"] = ['required', 'phone_number'];

        return $this;
    }
}
