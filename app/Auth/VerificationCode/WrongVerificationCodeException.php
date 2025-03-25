<?php

namespace App\Auth\VerificationCode;

use Exception;

/**
 * Class WrongVerificationCodeException
 *
 * @package App\Auth\VerificationCode
 */
class WrongVerificationCodeException extends Exception
{
    public function __construct()
    {
        parent::__construct('Verification Code is wrong.');
    }
}
