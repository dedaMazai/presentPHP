<?php

namespace App\Services\Deal\Exceptions;

use Exception;

/**
 * Class ChangingTypeOfPaymenNotAllowedException
 *
 * @package App\Services\Deal\Exceptions
 */
class ChangingTypeOfPaymenNotAllowedException extends Exception
{
    protected $message = 'Изменение типа формы оплаты для заявки не допускается';
    protected $code = 409;
}
