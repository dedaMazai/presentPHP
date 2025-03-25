<?php

namespace App\Auth\VerificationCode\Notification;

use Illuminate\Notifications\Notification;

/**
 * Class VerificationCodeNotification
 *
 * @package App\Auth\VerificationCode\Notification
 */
class VerificationCodeNotification extends Notification
{
    public function __construct(
        private string $code
    ) {
    }

    public function via(): array
    {
        return ['sms'];
    }

    public function toSms(mixed $notifiable): string
    {
        return "{$this->code} - ваш код подтверждения";
    }
}
