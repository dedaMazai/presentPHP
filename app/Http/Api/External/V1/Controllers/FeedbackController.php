<?php

namespace App\Http\Api\External\V1\Controllers;

use App\Http\Resources\Feedback\FeedbackHistoryCollection;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\Feedback\Dto\SaveFeedbackDto;
use App\Services\Feedback\FeedbackService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FeedbackController
 *
 * @package App\Http\Api\External\V1\Controllers
 */
class FeedbackController extends Controller
{
    public function __construct(private FeedbackService $feedbackService)
    {
    }

    /**
     * @throws ValidationException
     * @throws AuthenticationException
     */
    public function sendAppeal(Request $request): Response
    {
        $this->validate($request, [
            'name' => 'string',
            'message' => 'required|string',
            'app_version' => 'string',
            'os_version' => 'string',
            'phone' => 'string',
            'email' => 'string'
        ]);

        try {
            $user_id = $this->getAuthUser()?->id;
        } catch (\Throwable) {
            $user_id = null;
        }

        $appeal = new SaveFeedbackDto(
            message: $request->message,
            name: $request->name,
            email: $request->email,
            user_id: $user_id,
            app_version: $request->app_version??null,
            os_version: $request->os_version??null,
            phone: $request->phone??null
        );


        $this->feedbackService->sendAppeal($appeal);

        return $this->empty();
    }

    /**
     * @throws ValidationException
     * @throws AuthenticationException
     */
    public function getFeedbackHistory(Request $request): Response
    {
        $this->validate($request, [
            'page' => 'int',
        ]);

        $user = $this->getAuthUser();

        $feedbackHistory = $this->feedbackService->getHistory($user->id);

        return response()->json(new FeedbackHistoryCollection($feedbackHistory->paginate()));
    }

    /**
     * @throws BadRequestException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws ValidationException
     */
    public function orderCallback(Request $request): Response
    {
        $this->validate($request, [
            'phone' => 'required|phone_number',
        ]);

        $this->feedbackService->orderCallback($this->getAuthUser(), $request->input('phone'));

        return $this->empty();
    }
}
