<?php

namespace App\Http\Api\External\V1\Controllers;

use App\Http\Resources\NotificationCollection;
use App\Jobs\ViewChunkNotificationJob;
use App\Models\Notification\Notification;
use App\Models\User\User;
use App\Services\Document\DocumentRepository;
use App\Services\Notification\NotificationRepository;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class NotificationController
 *
 * @package App\Http\Api\External\V1\Controllers
 */
class NotificationController extends Controller
{
    public function __construct(private NotificationRepository $repository)
    {
    }

    public function index(): Response
    {
        $query = Notification::forAllUsers();
        /** @var User|null $user */
        if ($user = $this->guard()->user()) {
            $query = Notification::forUser($user);
        }

        $notifications = $query->latest()->paginate();

        return response()->json(new NotificationCollection(['notifications' => $notifications, 'user' => $user]));
    }

    /**
     * @throws AuthenticationException
     */
    public function view(): Response
    {
        $user = $this->getAuthUser();

        $notifications = Notification::notViewedByUser($user)
            ->get();
        if ($notifications->count() > 1000) {
            foreach ($notifications->chunk(1000) as $notificationChunk) {
                ViewChunkNotificationJob::dispatch($notificationChunk, $user)->onQueue('default');
            }
            sleep($notifications->count() / 1000);
        } else {
            $notifications->each(fn(Notification $notification) => $notification->markAsViewed($user));
        }

        return $this->empty();
    }

    public function getState(): Response
    {
        $hasNewMessages = Notification::forUser($this->getAuthUser())->notViewedByUser($this->getAuthUser())->get();

        $hasNewCommunications = $this->repository->getNewCommunication($this->getAuthUser()->crm_id);

        return $this->response(
            ['has_new_messages' => count($hasNewMessages)>0, 'has_new_communication' => $hasNewCommunications]
        );
    }

    public function read(string $notificationId): Response
    {
        $user = $this->getAuthUser();

        $notification = Notification::find($notificationId);

        if (!$notification) {
            return $this->response()->setStatusCode(404);
        }

        if (!Notification::notViewedByUser($user)->where('id', $notificationId)->first()) {
            return $this->response()->setStatusCode(409);
        }

        $notification?->markAsViewed($user);

        return $this->response()->setStatusCode(204);
    }
}
