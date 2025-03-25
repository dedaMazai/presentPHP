<?php

namespace App\Validation;

use App\Exceptions\ValidationException;
use Illuminate\Support\Facades\Validator;

abstract class AbstractValidationService implements ValidationInterface
{
    protected array $validation_rules;
    protected array $validation_messages;
    protected array $inputs;
    protected $validator;

    public function __construct(array $inputs = [])
    {
        $this->inputs = $inputs;
        $this->validation_rules = [];
        $this->validation_messages = [];
    }

    /**
     * @param array $inputs
     * @return ValidationInterface
     */
    public function setInputs(array $inputs): ValidationInterface
    {
        $this->clearValidation();
        $this->inputs = $inputs;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * @param mixed $validator
     * @return AbstractValidationService
     */
    public function setValidator($validator)
    {
        $this->validator = $validator;
        return $this;
    }

    public function clearValidation()
    {
        $this->validation_rules = [];
        $this->validation_messages = [];
    }

    public function validate()
    {
        return Validator::make($this->inputs, $this->validation_rules, $this->validation_messages);
    }

    public function validateData()
    {
        $this->validator = Validator::make($this->inputs, $this->validation_rules, $this->validation_messages);
        if ($this->validator->fails()) {
            throw new ValidationException("Ошибка валидации.");
        }
    }

    public function response()
    {
        return $this->validator->errors()->toArray();
    }

    public function dbIdMessageRule(string $key)
    {
        $min = 1;
        $max = 9223372036854775807;
        $this->validation_rules["$key"] = ['required', 'integer', 'digits_between:1,9223372036854775807'];
        $this->validation_messages["$key.required"] = 'Необходимое значение.';
        $this->validation_messages["$key.integer"] = 'Значение должно быть целым числом.';
        $this->validation_messages["$key.digits_between"] = "Значение должно быть между $min и $max.";
    }

    public function dbBooleanMessageRule(string $key)
    {
        $this->validation_rules["$key"] = ['required', 'boolean'];
        $this->validation_messages["$key.required"] = 'fill_able';
        $this->validation_messages["$key.boolean"] = 'incorrectly';
    }

    public function validateId()
    {
        $key = 'id';

        $this->dbIdMessageRule($key);

        return $this;
    }

    public function validatePage()
    {
        $key = 'page';

        if (isset($this->inputs[$key])) {
            $this->validation_rules["$key"] = ['required', 'integer', 'digits_between:1,9223372036854775807'];
            $this->validation_messages["$key.required"] = 'fill_able';
            $this->validation_messages["$key.integer"] = 'incorrectly';
        }

        return $this;
    }

    public function validateRequired($key)
    {
        $this->validation_rules["$key"] = ['required'];
        $this->validation_messages["$key.required"] = 'Значение не указано.';

        return $this;
    }

    public function validateBooleanData(string $key, bool $required = true): ValidationInterface
    {
        if (!$required and !isset($this->inputs[$key])) {
            return $this;
        }

        $this->dbBooleanMessageRule($key);

        return $this;
    }
}
