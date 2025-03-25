<?php

namespace App\Services\Notification;

use App\Models\UnauthorizedPushtokens;
use App\Models\Notification\Notification;
use App\Models\Notification\NotificationDestinationType;
use App\Models\User\User;

/**
 * Class UserFinder
 *
 * @package App\Services\Notification
 */
class UserFinder
{
    /**
     * @param Notification $notification
     *
     * @return User[]
     */
    public function findAllByNotification(Notification $notification): array
    {
        return match ($notification->destination_type->value) {
            NotificationDestinationType::singleCrmUser()->value =>
            $this->findSingleCrmUser($notification->destination_type_payload),
            NotificationDestinationType::singleUserByPhone()->value =>
            $this->findSingleUserByPhone($notification->destination_type_payload),
            default => [],
        };
    }

    /**
     * @return User[]
     */
    private function findSingleCrmUser(array $payload): array
    {
        if (!isset($payload['crm_id'])) {
            return [];
        }

        return [User::whereCrmId($payload['crm_id'])->first()];
    }

    /**
     * @return User[]
     */
    private function findSingleUserByPhone(array $payload): array
    {
        if (!isset($payload['phone'])) {
            return [];
        }
        $phone = preg_replace('/[^+0-9.]+/', '', $payload['phone']);
        return [User::wherePhone($phone)->first()];
    }
}
