<?php

namespace App\Validation;

interface ValidationInterface
{
    public function __construct(array $inputs = []);
    public function validate();
}
