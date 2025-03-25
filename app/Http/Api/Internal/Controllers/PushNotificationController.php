<?php

namespace App\Http\Api\Internal\Controllers;

use App;
use App\Http\Api\Internal\Requests\CreatePushNotificationRequest;
use App\Models\User\User;
use Illuminate\Contracts\Foundation\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class PushNotificationController
 *
 * @package App\Http\Api\Internal\Controllers
 */
class PushNotificationController extends Controller
{
    /**
     * @var Application|mixed
     */
    private mixed $messaging;

    public function __construct()
    {
        $this->messaging = app('firebase.messaging');
    }

    public function subscribeByUsersCrmId(CreatePushNotificationRequest $request): Response
    {
        $topics = $request->input('topics');
        $usersPushToken = $this->findUsersTokenId($request->input('users_crm_id'));
        $response = [];

        if (!empty($usersPushToken)) {
            $response = $this->messaging->subscribeToTopics($topics, $usersPushToken);

            logger()?->debug('subscribeByUsersCrmId', $response);
        }

        return $this->response(['response' => $response]);
    }

    public function unsubscribeByUsersCrmId(CreatePushNotificationRequest $request): Response
    {
        $topics = $request->input('topics');
        $usersPushToken = $this->findUsersTokenId($request->input('users_crm_id'));
        $response = [];

        if (!empty($usersPushToken)) {
            $response = $this->messaging->unsubscribeFromTopics($topics, $usersPushToken);

            logger()?->debug('unsubscribeByUsersCrmId', $response);
        }

        return $this->response(['response' => $response]);
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

    private function findUsersTokenId(array $crmIds): array
    {
        $usersPushToken = [];

        foreach ($crmIds as $crmId) {
            $user = $this->findUser($crmId);

            if ($user->push_token) {
                $usersPushToken[] = $user->push_token;
            }
        }

        return $usersPushToken;
    }
}
