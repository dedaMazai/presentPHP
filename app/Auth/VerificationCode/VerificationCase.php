<?php

namespace App\Auth\VerificationCode;

use Spatie\Enum\Enum;

/**
 * Class VerificationCase
 *
 * @method static self login()
 * @method static self registration()
 * @method static self passwordReset()
 * @method static self claimAcceptance()
 * @method static self deleteAccount()
 *
 * @package App\Auth\User
 */
class VerificationCase extends Enum
{
    protected static function values()
    {
        return [
            'login' => 'login',
            'registration' => 'registration',
            'passwordReset' => 'password_reset',
            'claimAcceptance' => 'claim_acceptance',
            'deleteAccount' => 'delete_account',
        ];
    }
}
