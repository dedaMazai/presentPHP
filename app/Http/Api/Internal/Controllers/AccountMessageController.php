<?php

namespace App\Http\Api\Internal\Controllers;

use App\Http\Api\Internal\Requests\CreateAccountDebtMessageRequest;
use App\Http\Api\Internal\Requests\CreateClaimMessageNotificationRequest;
use App\Jobs\SendPush;
use App\Models\Claim\ClaimMessage\ClaimMessageState;
use App\Models\User\User;
use App\Services\Claim\ClaimMessageRepository;
use App\Services\Notification\NotificationService;
use Exception;
use Psr\SimpleCache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ClaimMessageController
 *
 * @package App\Http\Api\Internal\Controllers
 */
class AccountMessageController extends Controller
{
    public function __construct(private NotificationService $notificationService)
    {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function newAccountsDebtMessage(
        CreateAccountDebtMessageRequest $request,
    ): Response {
        foreach ($request->input('user_crm_ids') as $userCrmId) {
            try {
                $this->findUser($userCrmId);
            } catch (\Throwable $exception) {
                continue;
            }

            $notification = $this->notificationService->createNewAccountDebtMessageNotification(
                title: $request->input('title'),
                text: $request->input('text'),
                crmUserId: strtolower($userCrmId),
            );

            SendPush::dispatch($notification)->onQueue('send_push');
        }


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
