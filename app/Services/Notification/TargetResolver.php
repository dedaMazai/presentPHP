<?php

namespace App\Services\Notification;

use App\Models\Notification\Notification;
use App\Models\Notification\NotificationDestinationType;
use App\Models\Notification\NotificationType;
use App\Models\UnauthorizedPushtokens;
use App\Models\User\User;
use App\Services\PushNotifier\Notification\Target;
use App\Services\PushNotifier\Notification\Target\ChannelTarget;
use App\Services\PushNotifier\Notification\Target\DeviceTarget;

class TargetResolver
{
    /**
     * @param Notification $notification
     *
     * @return Target[]
     */
    public function resolveByNotification(Notification $notification): array
    {
        $channels = [];

        $topic_test = app()->environment(['local', 'staging']) ? '_test' : '';

        $general = 'General';

        $news = 'News';
        $company_news = 'company_news';

        $notif = 'Notification';
        $project_uk = 'ProjectUK';
        $owners = 'Owners';
        $customers = 'Customers';
        $reality_types = 'RealtyTypes';
        $build = 'Corp';
        $role = 'Role';

        $project_ids = !empty($notification->destination_type_payload['project_ids'])
            ? !is_array($notification->destination_type_payload['project_ids'])
                ? [$notification->destination_type_payload['project_ids']]
                : $notification->destination_type_payload['project_ids'] : [];
        $uk_project_ids = !empty($notification->destination_type_payload['uk_project_ids'])
            ? !is_array($notification->destination_type_payload['uk_project_ids'])
                ? [$notification->destination_type_payload['uk_project_ids']]
                : $notification->destination_type_payload['uk_project_ids'] : [];
        $buildings_id = !empty($notification->destination_type_payload['buildings_id'])
            ? !is_array($notification->destination_type_payload['buildings_id'])
                ? [$notification->destination_type_payload['buildings_id']]
                : $notification->destination_type_payload['buildings_id'] : [];
        $roles_id = !empty($notification->destination_type_payload['client_role_types'])
            ? !is_array($notification->destination_type_payload['client_role_types'])
                ? [$notification->destination_type_payload['client_role_types']]
                : $notification->destination_type_payload['client_role_types'] : [];
        $account_realty_types = !empty($notification->destination_type_payload['account_realty_types'])
            ? !is_array($notification->destination_type_payload['account_realty_types'])
                ? [$notification->destination_type_payload['account_realty_types']]
                : $notification->destination_type_payload['account_realty_types'] : [];

        if ($notification->destination_type->equals(NotificationDestinationType::allUsers())) {
            array_push(
                $channels,
                new ChannelTarget($general . $topic_test)
            );

            return $channels;
        }

        if ($notification->destination_type->equals(NotificationDestinationType::customersByProjects())) {
            if ($project_ids) {
                foreach ($project_ids as $project_id) {
                    array_push(
                        $channels,
                        new ChannelTarget($notif . '_' . $customers . '_' . $project_id . $topic_test)
                    );
                }
            } else {
                array_push(
                    $channels,
                    new ChannelTarget($notif . '_' . $customers . $topic_test)
                );
            }

            return $channels;
        }

        if ($notification->destination_type->equals(NotificationDestinationType::ownersByUkProjects())) {
            if ($uk_project_ids) {
                foreach ($uk_project_ids as $uk_project_id) {
                    if ($buildings_id) {
                        foreach ($buildings_id as $building_id) {
                            if ($roles_id) {
                                foreach ($roles_id as $role_id) {
                                    array_push(
                                        $channels,
                                        new ChannelTarget($notif . '_' . $owners . '_' . $project_uk . '_' .
                                            $uk_project_id . '_' . $build . '_' . $building_id . '_' . $role . '_' .
                                            $role_id . $topic_test)
                                    );
                                }
                            } else {
                                array_push(
                                    $channels,
                                    new ChannelTarget($notif . '_' . $owners . '_' . $project_uk . '_' .
                                        $uk_project_id . '_' . $build . '_' . $building_id . $topic_test)
                                );
                            }
                        }
                    } elseif ($roles_id) {
                        foreach ($roles_id as $role_id) {
                            array_push(
                                $channels,
                                new ChannelTarget($notif . '_' . $owners . '_' . $project_uk . '_' .
                                    $uk_project_id . '_' . $role . '_' . $role_id . $topic_test)
                            );
                        }
                    } else {
                        array_push(
                            $channels,
                            new ChannelTarget($notif . '_' . $owners . '_' . $project_uk . '_' .
                                $uk_project_id . $topic_test)
                        );
                    }
                }
            } elseif ($roles_id) {
                foreach ($roles_id as $role_id) {
                    array_push(
                        $channels,
                        new ChannelTarget($notif . '_' . $owners . '_' . $project_uk . '_' .
                            $role . '_' . $role_id . $topic_test)
                    );
                }
            } else {
                array_push(
                    $channels,
                    new ChannelTarget($notif . '_' . $owners . '_' . $project_uk . $topic_test)
                );
            }

            return $channels;
        }

        if ($notification->destination_type->equals(NotificationDestinationType::ownersByAccountRealtyTypes())) {
            if ($account_realty_types) {
                foreach ($account_realty_types as $account_realty_type_group) {
                    foreach ($account_realty_type_group as $account_realty_type) {
                        if ($roles_id) {
                            foreach ($roles_id as $role_id) {
                                array_push(
                                    $channels,
                                    new ChannelTarget($notif . '_' . $owners . '_' . $reality_types . '_' .
                                        $account_realty_type . '_' . $role . '_' . $role_id . $topic_test)
                                );
                            }
                        } else {
                            array_push(
                                $channels,
                                new ChannelTarget($notif . '_' . $owners . '_' . $account_realty_type . $topic_test)
                            );
                        }
                    }
                }
            } else {
                array_push(
                    $channels,
                    new ChannelTarget($notif . '_' . $owners . '_' . $reality_types . $topic_test)
                );
            }

            return $channels;
        }

        if ($notification->destination_type->equals(NotificationDestinationType::companyNewsSubscribers())) {
            array_push(
                $channels,
                new ChannelTarget($news . '_' . $company_news . $topic_test)
            );

            return $channels;
        }

        if ($notification->destination_type->equals(NotificationDestinationType::allUkUsers())) {
            array_push(
                $channels,
                new ChannelTarget($news . '_' . $project_uk . $topic_test)
            );

            return $channels;
        }

        if ($notification->destination_type->equals(NotificationDestinationType::usersByUkAndBuilding())) {
            if ($uk_project_ids) {
                foreach ($uk_project_ids as $uk_project_id) {
                    if ($buildings_id) {
                        foreach ($buildings_id as $building_id) {
                            array_push(
                                $channels,
                                new ChannelTarget($news . '_' . $project_uk . '_' .
                                    $uk_project_id . '_' . $build . '_' .$building_id . $topic_test)
                            );
                        }
                    } else {
                        array_push(
                            $channels,
                            new ChannelTarget($news . '_' . $project_uk . '_' .
                                $uk_project_id . $topic_test)
                        );
                    }

                    return $channels;
                }
            } else {
                array_push(
                    $channels,
                    new ChannelTarget($news . '_' . $project_uk . $topic_test)
                );
            }

            return $channels;
        }

        if ($notification->destination_type->equals(NotificationDestinationType::unauthorizedUsers())) {
            $recipients = UnauthorizedPushtokens::get()->all();

            return array_map(fn($recipient) => new DeviceTarget($recipient->push_token), $recipients);
        }

        return $this->resolveTargets($notification);
    }

    /**
     * @param Notification $notification
     *
     * @return DeviceTarget[]
     */
    private function resolveTargets(Notification $notification): array
    {
        $recipients = $notification
            ->recipients()
            ->get()
            ->filter(fn (User $user) => $this->canReceivePushNotification($user, $notification))
            ->all();

        return array_map(fn(User $recipient) => new DeviceTarget($recipient->push_token), $recipients);
    }

    private function canReceivePushNotification(User $user, Notification $notification): bool
    {
        if (empty($user->push_token)) {
            return false;
        }
        if ($notification->type->equals(NotificationType::news())) {
            return $user->canReceiveObjectNewsPush();
        }

        return true;
    }
}
