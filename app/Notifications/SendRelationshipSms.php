<?php

namespace App\Notifications;

use App\Services\RelationshipInvite\Dto\SaveRelationshipInviteDto;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

/**
 * Class VerificationCodeNotification
 *
 * @package App\Auth\VerificationCode\Notification
 */
class SendRelationshipSms extends Notification
{
    public function __construct(
        private SaveRelationshipInviteDto $inviteDto
    ) {
    }

    public function via(): array
    {
        return ['sms'];
    }

    public function toSms(mixed $notifiable): string
    {
        $ios_link = "https://$_SERVER[HTTP_HOST]/ios";
        $android_link = "https://$_SERVER[HTTP_HOST]/android";

        return  $this->inviteDto->firstName .
                ", Вам предоставлен доступ в мобильном приложении 'Pioneer' к объекту по адресу "
                .$this->inviteDto->account->getAddress() . ". \r\n".
                "Уровень доступа - Проживающий. \r\n".
                "Инициатор - ". $this->inviteDto->owner->first_name . " " . $this->inviteDto->owner->last_name .
                " " . $this->inviteDto->owner->middle_name. ". \r\n".
                "Скачать для iOS - $ios_link \r\n".
                "Скачать для android - $android_link \r\n";
    }
}
