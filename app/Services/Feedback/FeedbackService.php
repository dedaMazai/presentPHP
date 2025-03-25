<?php

namespace App\Services\Feedback;

use App\Models\Feedback\Feedback;
use App\Models\User\User;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\Feedback\Dto\SaveFeedbackDto;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

/**
 * Class FeedbackService
 *
 * @package App\Services\Feedback
 */
class FeedbackService
{
    public function __construct(
        private DynamicsCrmClient $dynamicsCrmClient,
        private FeedbackApiClient $feedbackApiClient,
    ) {
    }

    public function sendAppeal(SaveFeedbackDto $dto): void
    {

        Feedback::create([
           'user_id' => $dto->user_id,
           'message' => $dto->message,
        ]);

        if ($dto->user_id && !$dto->phone) {
            $user = User::find($dto->user_id);
            $dto->name = $user->getFullName();
            $dto->email = $user->email;
            $dto->phone = $user->phone;
        }
        logger()->debug(
            'feedback log',
            [
                'name' => $dto->name,
                'email' => $dto->email,
                'phone' => $dto->phone,
            ]
        );

        $this->feedbackApiClient->createFeedback($dto);
    }

    public function getHistory(string $userId)
    {
        return Feedback::byUserId($userId)->orderBy('created_at', 'desc');
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function orderCallback(User $user, string $phone): void
    {
        $this->dynamicsCrmClient->orderCallback($user, $phone);
    }
}
