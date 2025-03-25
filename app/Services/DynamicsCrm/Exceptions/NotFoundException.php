<?php

namespace App\Services\DynamicsCrm\Exceptions;

use Exception;

/**
 * Class NotFoundException
 *
 * @package App\Services\DynamicsCrm\Exceptions
 */
class NotFoundException extends Exception
{
    protected $message = 'Not found.';

    protected $code = '404';
}
