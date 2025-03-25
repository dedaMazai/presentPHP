<?php


namespace App\Validation\Receipt;

use App\Validation\BaseValidation;
use App\Validation\ValidationInterface;

class ReceiptValidation extends BaseValidation
{
    public function validateStartDate(bool $required = true): ValidationInterface
    {
        $key = 'start_date';

        if (!$required and !isset($this->inputs[$key])) {
            return $this;
        }

        $this->validation_rules["$key"] = ['date'];

        return $this;
    }

    public function validateEndDate(bool $required = true): ValidationInterface
    {
        $key = 'end_date';

        if (!$required and !isset($this->inputs[$key])) {
            return $this;
        }

        $this->validation_rules["$key"] = ['date'];

        return $this;
    }
}
