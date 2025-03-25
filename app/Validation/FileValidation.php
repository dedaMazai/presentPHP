<?php

namespace App\Validation;

class FileValidation extends AbstractValidationService
{
    public function imageValidate(string $key)
    {
        $this->validation_rules["$key"] = ['mimes:jpeg,png,bmp,svg,webp', 'max:30720']; //30мб
        $this->validation_messages["$key.mimes"] = 'Доступные форматы:jpeg,png,bmp,svg,webp.';
        $this->validation_messages["$key.max"] = 'Файл превышает допустимый размер.';

        return $this;
    }
}
