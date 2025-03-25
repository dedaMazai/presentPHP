<?php

namespace App\Http\Api\External\V1\Controllers;

use App\Http\Resources\Claim\ClaimMessageCollection;
use App\Http\Resources\Claim\ClaimMessageResource;
use App\Http\Resources\Claim\Communications\CommunicationResource;
use App\Http\Resources\Claim\Files\ClaimFilesCollection;
use App\Models\Claim\ClaimMessage\ClaimMessageState;
use App\Services\Claim\ClaimMessageRepository;
use App\Services\Claim\ClaimMessageService;
use App\Services\Claim\ClaimRepository;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\Notification\NotificationRepository;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Psr\SimpleCache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ClaimMessageController
 *
 * @package App\Http\Api\V1\Controllers
 */
class ClaimMessageController extends Controller
{
    public function __construct(
        private ClaimMessageService $service,
        private ClaimMessageRepository $repository,
        private ClaimRepository $claimRepository,
        private DynamicsCrmClient $dynamicsCrmClient,
    ) {
    }

    /**
     * @throws AuthenticationException
     */
    public function getMessageState(string $claimId): Response
    {
        /** @var ClaimMessageState $state */
        $state = ClaimMessageState::byUserAndClaimId($this->getAuthUser(), $claimId)->first();

        return $this->response(['has_new_messages' => $state?->has_new_messages ?? false]);
    }

    /**
     * @throws InvalidArgumentException
     * @throws AuthenticationException
     */
    public function getMessages(string $claimId): Response
    {
        try {
            $claimMessages = $this->repository->getAll(
                claimId: $claimId,
                resetCache: true,
            );
        } catch (Exception) {
            $claimMessages = [];
        }

        try {
            $claimFiles = $this->repository->getFiles($claimId);
        } catch (Exception) {
            $claimFiles = [];
        }

        if ($claimFiles) {
            foreach ($claimFiles['documentList'] as $key => $value) {
                if (!isset($claimFiles['documentList'][$key]['urlPreview'])) {
                    continue;
                }

                $claimFiles['documentList'][$key]['urlPreview'] = $this->claimRepository
                    ->getBody($claimFiles['documentList'][$key]['urlPreview']);
            }
        }

        $this->service->createOrUpdateState($this->getAuthUser(), $claimId);
        $message_collection = $claimMessages?(new ClaimMessageCollection($claimMessages))->collection->toArray():[];
        $file_collection= $claimFiles?(new ClaimFilesCollection($claimFiles['documentList']))
            ->collection->toArray():[];

        return response()->json(array_merge($message_collection, $file_collection));
    }

    public function getPoolMessages(string $claimId): Response
    {
        while ($this->service->getState($this->getAuthUser(), $claimId) == false) {
            sleep(3);
        }

        try {
            $claimMessages = $this->repository->getAll(
                claimId: $claimId,
                resetCache: true,
            );
        } catch (Exception) {
            $claimMessages = [];
        }

        try {
            $claimFiles = $this->repository->getFiles($claimId);
        } catch (Exception) {
            $claimFiles = [];
        }

        if ($claimFiles) {
            foreach ($claimFiles['documentList'] as $key => $value) {
                if (!isset($claimFiles['documentList'][$key]['urlPreview'])) {
                    continue;
                }

                $claimFiles['documentList'][$key]['urlPreview'] = $this->claimRepository
                    ->getBody($claimFiles['documentList'][$key]['urlPreview']);
            }
        }

        $this->service->createOrUpdateState($this->getAuthUser(), $claimId);
        $message_collection = $claimMessages?(new ClaimMessageCollection($claimMessages))->collection->toArray():[];
        $file_collection= $claimFiles?(new ClaimFilesCollection($claimFiles['documentList']))
            ->collection->toArray():[];

        return response()->json(array_merge($message_collection, $file_collection));
    }

    /**
     * @throws NotFoundException
     * @throws ValidationException
     * @throws BadRequestException
     * @throws InvalidArgumentException
     * @throws AuthenticationException
     */
    public function sendMessage(string $claimId, Request $request): Response
    {
        $this->validate($request, [
            'text' => 'required|string',
        ]);

        $claimMessage = $this->service->send($claimId, $request->input('text'), $this->getAuthUser());

        return response()->json(new ClaimMessageResource($claimMessage));
    }

    public function sendFiles(string $claimId, Request $request): Response
    {
        $this->validate($request, [
            'text' => 'required|files',
        ]);

        $claimMessage = $this->service->sendFiles($claimId, $request->input('files'), $this->getAuthUser());

        return $this->response();
    }

    public function readCommunications(string $claimId, Request $request): Response
    {
        $communications = $this->service->readCommunication($claimId, $request, $this->getAuthUser());

        return response()->json(new CommunicationResource($communications));
    }
}
