<?php

namespace App\Services\Deal\Exceptions;

use Exception;

/**
 * Class InvalidPaymentTypeException
 *
 * @package App\Services\Deal\Exceptions
 */
class InvalidPaymentTypeException extends Exception
{
    protected $message = 'Некорректное значение paymentType';
    protected $code = 422;
}
