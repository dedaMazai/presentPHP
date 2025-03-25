<?php

namespace App\Services\PushNotifier;

use App\Services\PushNotifier\Notification\Target;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Messaging;
use App\Services\PushNotifier\Notification\Notification;
use App\Services\PushNotifier\Firebase\Translator\NotificationTranslator;

/**
 * Class FirebasePushNotifier
 *
 * @package App\Services\PushNotifier
 */
final class FirebasePushNotifier implements PushNotifier
{
    private NotificationTranslator $notificationTranslator;

    public function __construct(private Messaging $messaging)
    {
        $this->notificationTranslator = new NotificationTranslator();
    }

    /**
     * @throws FirebaseException|MessagingException
     */
    public function push(Notification $notification): void
    {
        if ($notification->isMulticasting()) {
            for ($count = 0; $count < count($notification->target()); $count++) {
                $message = $this->notificationTranslator->translate($notification, $count);

                $this->messaging->send($message);
            }
        } else {
            $message = $this->notificationTranslator->translate($notification);

            $this->messaging->send($message);
        }
    }
}
