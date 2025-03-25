<?php

namespace App\Http\Api\External\V2\Controllers;

use App\Http\Resources\V2\DocumentUserResource;
use App\Http\Resources\V2\UserDocumentCollection;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\V2\User\UserService;
use Illuminate\Auth\AuthenticationException;
use Psr\SimpleCache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use function response;

/**
 * Class UserDocumentController
 *
 * @package App\Http\Api\External\V2\Controllers
 */
class UserDocumentController extends Controller
{
    public function __construct(
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
        return response()->json(new DocumentUserResource([
            'document' => $this->userService->getDocuments($this->getAuthUser()),
            'user' => $this->getAuthUser()
        ]));
    }
}
