<?php

/** @var Illuminate\Routing\Router $router */

use App\Http\Api\Internal\Controllers\AccountMessageController;
use App\Http\Api\Internal\Controllers\NotificationController;
use App\Http\Api\Internal\Controllers\PushNotificationController;
use App\Http\Api\Internal\Controllers\PushTokenUnauthorizedController;
use App\Http\Api\Internal\Controllers\ClaimMessageController;

$router->post(
    'users/push/subscribe-to-topics',
    [PushNotificationController::class, 'subscribeByUsersCrmId']
);
$router->post(
    'users/push/unsubscribe-from-topics',
    [PushNotificationController::class, 'unsubscribeByUsersCrmId']
);

$router->post(
    'push-token-unauthorized',
    [PushTokenUnauthorizedController::class, 'createPushTokenUnauthorized']
);

$router->post(
    'users/{userCrmId}/notifications',
    [NotificationController::class, 'createByUserCrmId']
);

$router->post(
    'users/{userCrmId}/claim/{claimId}/manager-messages',
    [ClaimMessageController::class, 'newClaimManagerMessage']
);

$router->post(
    'users/accounts-debt',
    [AccountMessageController::class, 'newAccountsDebtMessage']
);
