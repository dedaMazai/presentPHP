<?php

namespace App\Http\Api\Internal\Controllers;

use App\Http\Api\Internal\Requests\CreateNotificationRequest;
use App\Jobs\SendPush;
use App\Models\Notification\NotificationType;
use App\Models\User\User;
use App\Services\Notification\NotificationService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class NotificationController
 *
 * @package App\Http\Api\Internal\Controllers
 */
class NotificationController extends Controller
{
    public function __construct(private NotificationService $service)
    {
    }

    public function createByUserCrmId(CreateNotificationRequest $request, string $userCrmId): Response
    {
        $user = $this->findUser($userCrmId);

        $path = storage_path('logs/crm_notification_requests.log');
        $text = date('Y-m-d H:i:s')."| $user | ". $request->input('text');

        file_put_contents($path, PHP_EOL . $text, FILE_APPEND);

        $notification = $this->service->createSingleCrmUserNotification(
            title: $request->input('title'),
            text: $request->input('text'),
            type: NotificationType::from($request->input('type')),
            crmUserId: strtolower($userCrmId),
            actionType: $request->input('action')['type'] ?? null,
            actionPayload: $request->input('action')['payload'] ?? null,
        );

        $this->service->setRecipients($notification);

        SendPush::dispatch($notification)->onQueue('send_push');

        return $this->empty();
    }

    private function findUser(string $crmId): User
    {
        /* @var User $user */
        $user = User::whereCrmId($crmId)->first();
        if ($user === null) {
            throw new NotFoundHttpException('User not found.');
        }

        return $user;
    }
}
