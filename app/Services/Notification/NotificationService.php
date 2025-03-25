<?php

namespace App\Services\Notification;

use App\Models\News\News;
use App\Models\Notification\Notification;
use App\Models\Notification\NotificationDestinationType;
use App\Models\Notification\NotificationType;
use App\Models\User\User;

class NotificationService
{
    public function __construct(
        private UserFinder $userFinder,
    ) {
    }

    public function createNewsNotification(News $news): Notification
    {
        $notification = $news->isCommon()
            ? $this->createCommonNewsNotification($news)
            : $this->createUkNewsNotification($news);

        $notification->updateAction('news', ['news_id' => $news->id]);

        return $notification->fresh();
    }

    public function createSingleCrmUserNotification(
        string $title,
        string $text,
        NotificationType $type,
        string $crmUserId,
        ?string $actionType = null,
        ?array $actionPayload = null,
    ): Notification {
        /** @var Notification $notification */
        $notification = Notification::create([
            'title' => $title,
            'text' => $text,
            'type' => $type,
            'destination_type' => NotificationDestinationType::singleCrmUser(),
            'destination_type_payload' => ['crm_id' => $crmUserId],
        ]);

        if ($actionType) {
            $notification->updateAction($actionType, $actionPayload);
        }

        return $notification->fresh();
    }

    public function createNewClaimMessageNotification(
        string $title,
        string $text,
        string $claimId,
        string $crmUserId,
        string $accountNumber
    ): Notification {
        return $this->createSingleCrmUserNotification(
            title: $title,
            text: $text,
            type: NotificationType::claim(),
            crmUserId: $crmUserId,
            actionType: 'new_claim_message',
            actionPayload: ['claim_id' => $claimId, 'account_number' => $accountNumber]
        );
    }

    public function createNewAccountDebtMessageNotification(
        string $title,
        string $text,
        string $crmUserId,
    ): Notification {
        return $this->createSingleCrmUserNotification(
            title: $title,
            text: $text,
            type: NotificationType::uk(),
            crmUserId: $crmUserId,
            actionType: 'mass_debt',
            actionPayload: ['mass_debt' => 'mass_debt']
        );
    }

    private function createUkNewsNotification(News $news): Notification
    {
        return Notification::create([
            'title' => $news->title,
            'text' => $news->description ?? "",
            'type' => NotificationType::news(),
            'destination_type' => (new Notification)->getDestinationTypeAttribute($news->destination),
            'destination_type_payload' => [
                'uk_project_ids' => [$news->uk_project_id],
                'buildings_id' => $news->buildings_id
            ]
        ]);
    }

    private function createCommonNewsNotification(News $news): Notification
    {
        return Notification::create([
            'title' => $news->title,
            'text' => $news->description ?? "",
            'type' => NotificationType::news(),
            'destination_type' => (new Notification)->getDestinationTypeAttribute($news->destination),
        ]);
    }

    public function setRecipients(Notification $notification): Notification
    {
        $recipients = $this->userFinder->findAllByNotification($notification);
        $recipientsIds = collect($recipients)->map(fn(User $user) => $user->id)->toArray();
        $this->syncRecipients($notification, $recipientsIds);

        return $notification->fresh();
    }

    private function syncRecipients(Notification $notification, array $recipientsIds): void
    {
        if (!empty($recipientsIds)) {
            $notification->recipients()->sync($recipientsIds);
        }
    }
}
