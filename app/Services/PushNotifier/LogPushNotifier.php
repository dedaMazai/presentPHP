<?php

namespace App\Services\PushNotifier;

use App\Services\PushNotifier\Notification\Notification;
use App\Services\PushNotifier\Notification\Target;
use Psr\Log\LoggerInterface;
use function get_class;
use function sprintf;

/**
 * Class LogPushNotifier
 *
 * @package App\Services\PushNotifier
 */
final class LogPushNotifier implements PushNotifier
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function push(Notification $notification): void
    {
        $target = is_array($notification->target()) ?
            collect($notification->target())->map(fn(Target $target) => $target->getValue())->join(', ') :
            collect($notification->target())->join(', ');

        $this->logger->debug(
            sprintf(
                'Push notification "%s" to "%s"',
                $notification->message()->title(),
                $target
            ),
            [
                'message' => $notification->message()->toArray(),
                'target' => [
                    'type' => $notification->isMulticasting() ?
                        get_class($notification->target()[0]) . '[]' : get_class($notification->target()),
                    'value' => $target,
                ],
                'meta' => $notification->meta()->toArray(),
            ]
        );
    }
}
