<?php

namespace App\Services\DynamicsCrm\Exceptions;

use Exception;

/**
 * Class UnableToCancelClaimException
 *
 * @package App\Services\DynamicsCrm\Exceptions
 */
class UnableToCancelClaimException extends Exception
{
    protected $message = 'Unable to cancel claim.';
}
