<?php


namespace App\Validation\Meter;

use App\Models\Meter\MeterSubtype;
use App\Models\Meter\MeterType;
use App\Validation\BaseValidation;
use App\Validation\ValidationInterface;
use Illuminate\Validation\Rule;

class MeterValidation extends BaseValidation
{
    public function validateType(bool $required = true): ValidationInterface
    {
        $key = 'type';

        if (!$required and !isset($this->inputs[$key])) {
            return $this;
        }

        $this->validation_rules["$key"] = [Rule::in(MeterType::toValues())];

        return $this;
    }

    public function validateSubtype(bool $required = true): ValidationInterface
    {
        $key = 'subtype';

        if (!$required and !isset($this->inputs[$key])) {
            return $this;
        }

        $this->validation_rules["$key"] = [Rule::in(MeterSubtype::toValues())];

        return $this;
    }
}
