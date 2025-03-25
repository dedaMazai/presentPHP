<?php

namespace App\Services\Deal\Exceptions;

use Exception;

/**
 * Class ChangingTypeOfPaymenNotAllowedException
 *
 * @package App\Services\Deal\Exceptions
 */
class MissingPaymentModeCodeException extends Exception
{
    protected $message = 'Не удалость определить форму оплаты (отсутствует paymentModeCode)';
    protected $code = 409;
}
