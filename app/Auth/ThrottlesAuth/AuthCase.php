<?php

namespace App\Auth\ThrottlesAuth;

use Spatie\Enum\Enum;

/**
 * Class AuthCase
 *
 * @method static self loginByPassword()
 * @method static self loginByVerificationCode()
 * @method static self resetPassword()
 *
 * @package App\Auth\ThrottlesAuth
 */
class AuthCase extends Enum
{
    protected static function values(): array
    {
        return [
            'loginByPassword' => 'login_by_password',
            'loginByVerificationCode' => 'login_by_verification_code',
            'resetPassword' => 'reset_password',
        ];
    }
}
