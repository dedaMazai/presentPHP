<?php

namespace App\Services\Notification;

use App\Models\Action;
use App\Models\Notification\Notification;
use App\Services\PushNotifier\Notification\Message;
use App\Services\PushNotifier\Notification\Notification as PushNotification;
use App\Services\PushNotifier\PushNotifier;

/**
 * Class PushService
 *
 * @package App\Services\Notification
 */
class PushService
{
    public function __construct(
        private PushNotifier $notifier,
        private TargetResolver $targetResolver,
    ) {
    }

    public function send(Notification $notification): void
    {
        $action = $this->prepareAction($notification->action);
        $message = Message::create(
            title: $notification->title,
            body: $notification->text ?? '',
            clickAction: $action
        );

        $targets = $this->targetResolver->resolveByNotification($notification);

        if (!empty($targets)) {
            $notification = PushNotification::create($message, ...$targets);

            if (!empty($action)) {
                $notification->withMeta(['action' => $action]);
            }

            $this->notifier->push($notification);
        }
    }

    private function prepareAction(?Action $action): ?string
    {
        if ($action) {
            return json_encode(['type' => $action->type, 'payload' => $action->payload]);
        }

        return null;
    }
}
