<?php

namespace App\Services\Claim;

use App\Models\Claim\Claim;
use App\Models\Claim\ClaimMessage\ClaimMessage;
use App\Models\Claim\ClaimMessage\ClaimMessageState;
use App\Models\Notification\Notification;
use App\Models\User\User;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\Notification\NotificationRepository;
use Illuminate\Support\Facades\Cache;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class ClaimMessageService
 *
 * @package App\Services\Claim
 */
class ClaimMessageService
{
    public function __construct(
        private ClaimMessageRepository $messageRepository,
        private DynamicsCrmClient $dynamicsCrmClient
    ) {
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     * @throws InvalidArgumentException
     */
    public function send(string $claimId, string $text, User $user): ClaimMessage
    {
        return $this->messageRepository->save($claimId, $text, $user);
    }

    public function sendFiles(string $claimId, string $files, User $user): ClaimMessage
    {
        return $this->messageRepository->save($claimId, $files, $user);
    }

    public function createOrUpdateState(User $user, string $claimId): ClaimMessageState
    {
        return ClaimMessageState::updateOrCreate(
            [
                'user_id' => $user->id,
                'claim_id' => $claimId,
            ],
            [
                'has_new_messages' => false,
            ]
        );
    }

    public function readCommunication(string $claimId, $body, User $user)
    {
        $response = [];

        if ($body->messages) {
            $messages = $body->messages ? $this->messageRepository->readMessages($body->messages) : [];
            foreach ($body->messages as $key => $value) {
                if ($messages['responceActionList'][$key]['isRead'] == true) {
                    $response['messages'][$key] = ['message_id' => $value, 'is_read' => true];
                    $rawClaim = $this->dynamicsCrmClient->getClaimById($claimId);
                    $accountNumber = $rawClaim['personalAccount'];
                    $cachedClaims = collect(Cache::get('claims.'.$accountNumber));
                    $cachedClaims = $cachedClaims->map(function (Claim $claim) use ($rawClaim, $claimId) {
                        if ($claim->getId() == $claimId) {
                            $claim->setReadSMS();
                        }
                        return $claim;
                    });
                    Cache::put('claims.'.$accountNumber, $cachedClaims);
                } else {
                    $response['messages'][$key] = ['message_id' => $value, 'is_read' => false];
                }
            }
        }

        if ($body->documents) {
            $documents = $body->documents?$this->messageRepository->readDocuments($body->documents):[];
            foreach ($body->documents as $key => $value) {
                if ($documents['responceActionList'][$key]['isRead'] == true) {
                    $response['documents'][$key] = ['document_id' => $value, 'is_read' => true];
                    $rawClaim = $this->dynamicsCrmClient->getClaimById($claimId);
                    $accountNumber = $rawClaim['personalAccount'];
                    $cachedClaims = collect(Cache::get('claims.'.$accountNumber));
                    $cachedClaims = $cachedClaims->map(function (Claim $claim) use ($rawClaim, $claimId) {
                        if ($claim->getId() == $claimId) {
                            $claim->setReadDocument();
                        }
                        return $claim;
                    });
                    Cache::put('claims.'.$accountNumber, $cachedClaims);
                } else {
                    $response['documents'][$key] = ['document_id' => $value, 'is_read' => false];
                }
            }
        }

        Notification::join('notification_user', 'notifications.id', '=', 'notification_user.notification_id')
            ->where('notifications.type', 'claim')->where('user_id', $user->id)->delete();

        return $response;
    }

    public function getState(User $user, string $claim_id)
    {
        $hasNewMessages = ClaimMessageState::byUserAndClaimId($user, $claim_id)->where('has_new_message', '=', true);

        if ($hasNewMessages) {
            return true;
        } else {
            return false;
        }
    }
}
