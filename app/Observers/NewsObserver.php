<?php

namespace App\Observers;

use App\Jobs\SendPush;
use App\Models\News\News;
use App\Services\Notification\NotificationService;

/**
 * Class NewsObserver
 *
 * @package App\Observers
 */
class NewsObserver
{
    public function __construct(
        private NotificationService $notificationService
    ) {
    }

    public function created(News $news): void
    {
        if ($news->is_published && $news->should_send_notification) {
            $news->update(['is_sent' => true]);
        }
    }

    public function saving(News $news): void
    {
        if ($news->is_published && $news->should_send_notification && !$news->is_sent) {
            $notification = $this->notificationService->createNewsNotification($news);
            SendPush::dispatch($notification)->onQueue('send_push');
            $news->update(['is_sent' => true]);
        }
    }
}
