<?php

namespace App\Http\Api\External\V1\Controllers;

use App\Http\Api\External\V1\Requests\UploadUserDocumentRequest;
use App\Http\Resources\UserDocumentCollection;
use App\Http\Resources\UserDocumentResource;
use App\Models\Document\DocumentType;
use App\Services\Document\DocumentRepository;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\User\Dto\UploadUserDocumentDto;
use App\Services\User\UserService;
use Illuminate\Auth\AuthenticationException;
use Psr\SimpleCache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use function response;

/**
 * Class UserDocumentController
 *
 * @package App\Http\Api\External\V1\Controllers
 */
class UserDocumentController extends Controller
{
    public function __construct(
        private DocumentRepository $repository,
        private UserService $userService,
    ) {
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws AuthenticationException
     * @throws InvalidArgumentException
     */
    public function index(): Response
    {
        return response()->json(new UserDocumentCollection($this->userService->getDocuments($this->getAuthUser())));
    }

    /**
     * @throws BadRequestException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws InvalidArgumentException
     */
    public function download(string $documentId): Response
    {
        if (!$this->userService->hasDocument($this->getAuthUser(), $documentId)) {
            throw new NotFoundHttpException('Document not found.');
        }

        $document = $this->repository->getDocumentById($documentId);

        return response(base64_decode($document->getBody()))->header('Content-Type', $document->getMimeType());
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws AuthenticationException
     * @throws InvalidArgumentException
     */
    public function delete(string $documentId): Response
    {
        $user = $this->getAuthUser();
        if (!$this->userService->hasDocument($user, $documentId)) {
            throw new NotFoundHttpException('Document not found.');
        }

        $user->deleteDocument($documentId);

        return $this->empty();
    }

    /**
     * @throws BadRequestException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws InvalidArgumentException
     */
    public function upload(UploadUserDocumentRequest $request): Response
    {
        $dto = new UploadUserDocumentDto(
            type: DocumentType::from($request->input('type')),
            body: base64_encode($request->file('file')->getContent()),
            mimeType: $request->file('file')->getClientMimeType(),
            fileName: $request->file('file')->getClientOriginalName()
        );
        $this->userService->uploadDocument($this->getAuthUser(), $dto);

        return $this->empty();
    }
}
