<?php

namespace App\Services\PushNotifier\Firebase\Translator;

use App\Services\PushNotifier\Notification\Message;
use App\Services\PushNotifier\Notification\Target;
use App\Services\PushNotifier\Notification\Target\ChannelTarget;
use App\Services\PushNotifier\Notification\Target\DeviceTarget;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as CloudNotification;
use App\Services\PushNotifier\Notification\Notification;
use LogicException;

/**
 * Class NotificationTranslator
 *
 * @package App\Services\PushNotifier\Firebase\Translator
 */
final class NotificationTranslator
{
    public function translate(Notification $notification, int $count = 0): CloudMessage
    {
        $target = $notification->isMulticasting() ? $notification->target()[$count] : $notification->target();

        $cloudMessage = CloudMessage::withTarget(
            $this->getTargetType($target),
            $target->getValue()
        );

        $cloudMessage = $cloudMessage->withNotification(CloudNotification::create(
            $notification->message()->title(),
            $notification->message()->body(),
            $notification->message()->image()
        ))->withAndroidConfig(AndroidConfig::fromArray([
            'notification' => $this->prepareNotification($notification->message()),
            'data' => $notification->meta()->toArray(),
        ]));

        if ($notification->hasMeta()) {
            $cloudMessage = $cloudMessage->withData(
                $notification->meta()->toArray()
            );
        }

        return $cloudMessage;
    }

    private function prepareNotification(Message $message): array
    {
        $result = [];

        if (!empty($message->title())) {
            $result['title'] = $message->title();
        }

        if (!empty($message->body())) {
            $result['body'] = $message->body();
        }

        if (!empty($message->image())) {
            $result['image'] = $message->image();
        }

        if (!empty($message->clickAction())) {
            $result['click_action'] = "open_main_screen";
        }

        return $result;
    }

    private function getTargetType(Target $target): string
    {
        if ($target instanceof DeviceTarget) {
            return 'token';
        }

        if ($target instanceof ChannelTarget) {
            return 'topic';
        }

        throw new LogicException('Not supported target:' .  $target::class . '.');
    }
}
