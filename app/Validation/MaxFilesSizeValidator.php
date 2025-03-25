<?php

namespace App\Validation;

use Illuminate\Http\UploadedFile;

/**
 * Class MaxFilesSizeValidator
 *
 * @package App\Validation
 */
class MaxFilesSizeValidator
{
    public function validate($attribute, $value, $parameters, $validator): bool
    {
        $total_size = array_reduce($value, function ($sum, UploadedFile $item) {
            $sum += $item->getSize();

            return $sum;
        });

        // $parameters[0] in kilobytes

        return $total_size < $parameters[0] * 1024;
    }
}
