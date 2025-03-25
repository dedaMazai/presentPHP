<?php

namespace App\Jobs;

use App\Models\Notification\Notification;
use App\Services\Notification\NotificationService;
use App\Services\Notification\PushService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class SendPush
 *
 * @package App\Jobs
 */
class SendPush implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private Notification $notification
    ) {
        logger()->debug('SendPush->construct: completed');
    }

    public function handle(PushService $pushService, NotificationService $notificationService)
    {
        $this->notification = $notificationService->setRecipients($this->notification);
        $pushService->send($this->notification);
    }
}
