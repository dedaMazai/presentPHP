<?php

namespace App\Validation\Payment;

use App\Validation\BaseValidation;
use App\Validation\ValidationInterface;

class PaymentValidation extends BaseValidation
{
    public function validateAmountByCard(bool $required = true): ValidationInterface
    {
        $key = 'amount';

        if (!$required and !isset($this->inputs[$key])) {
            return $this;
        }

        $this->validation_rules["$key"] = ['required_without:claim_id', 'integer', 'min:1'];

        return $this;
    }

    public function validateAmountByApplePay(bool $required = true): ValidationInterface
    {
        $key = 'amount';

        if (!$required and !isset($this->inputs[$key])) {
            return $this;
        }

        $this->validation_rules["$key"] = ['required_without:claim_id', 'integer'];

        return $this;
    }

    public function validateClaimIdByCard(bool $required = true): ValidationInterface
    {
        $key = 'claim_id';

        if (!$required and !isset($this->inputs[$key])) {
            return $this;
        }

        $this->validation_rules["$key"] = ['required_without:amount', 'string'];

        return $this;
    }

    public function validateClaimIdByApplePay(bool $required = true): ValidationInterface
    {
        $key = 'claim_id';

        if (!$required and !isset($this->inputs[$key])) {
            return $this;
        }

        $this->validation_rules["$key"] = ['required_without:amount', 'string'];

        return $this;
    }

    public function validateTokenData(bool $required = true): ValidationInterface
    {
        $key = 'token_data';

        if (!$required and !isset($this->inputs[$key])) {
            return $this;
        }

        $this->validation_rules["$key"] = ['required'];

        return $this;
    }

    public function validateUrl(bool $required = true): ValidationInterface
    {
        $key = 'url';

        if (!$required and !isset($this->inputs[$key])) {
            return $this;
        }

        $this->validation_rules["$key"] = ['required', 'string'];

        return $this;
    }
}
