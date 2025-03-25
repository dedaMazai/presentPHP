<?php

namespace App\Http\Api\Internal\Controllers;

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
class ClaimMessageController extends Controller
{
    public function __construct(private NotificationService $notificationService)
    {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function newClaimManagerMessage(
        CreateClaimMessageNotificationRequest $request,
        string $userCrmId,
        string $claimId,
        ClaimMessageRepository $claimMessageRepository,
    ): Response {
        $user = $this->findUser($userCrmId);
        $this->updateState($user->id, $claimId);

        try {
            $claimMessageRepository->getAll(
                claimId: $claimId,
                resetCache: true,
            );
        } catch (Exception $e) {
            logger()->error('Getting claim messages', [
                'claimId' => $claimId,
                'message' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ]);
        }

        $notification = $this->notificationService->createNewClaimMessageNotification(
            title: $request->input('title'),
            text: $request->input('text'),
            claimId: $claimId,
            crmUserId: strtolower($userCrmId),
            accountNumber: $request->input('account_number')
        );

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

    private function updateState(int $userId, string $claimId): void
    {
        ClaimMessageState::updateOrCreate(
            [
                'user_id' => $userId,
                'claim_id' => $claimId,
            ],
            [
                'has_new_messages' => true,
            ]
        );
    }
}
