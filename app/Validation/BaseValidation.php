<?php


namespace App\Validation;

class BaseValidation extends AbstractValidationService
{
    protected array $validation_rules;
    protected array $validation_messages;
    protected array $inputs;
    protected $validator;


    public function __construct(array $inputs = [])
    {
        parent::__construct($inputs);
    }

    public function clearValidation()
    {
        parent::clearValidation();
    }

    /**
     * @param array $inputs
     * @return ValidationInterface
     */
    public function setInputs(array $inputs): ValidationInterface
    {
        parent::setInputs($inputs);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValidator()
    {
        parent::getValidator();
        return $this;
    }

    public function validate()
    {
        return parent::validate();
    }

    public function validateData()
    {
        parent::validateData();
    }

    public function response()
    {
        return parent::response();
    }

    public function dbIdMessageRule(string $key)
    {
        parent::dbIdMessageRule($key);
    }

    public function dbBooleanMessageRule(string $key)
    {
        parent::dbBooleanMessageRule($key);
    }

    public function validateId()
    {
        parent::validateId();
        return $this;
    }

    public function validatePage()
    {
        parent::validatePage();
        return $this;
    }
}
