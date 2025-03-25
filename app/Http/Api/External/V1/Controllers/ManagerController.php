<?php

namespace App\Http\Api\External\V1\Controllers;

use App\Http\Api\External\V1\Requests\Sales\ManagerContactsRequest;
use App\Http\Resources\Sales\ManagersInfoCollection;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\Manager\ManagerService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ManagerController
 *
 * @package App\Http\Api\External\V1\Controllers
 */
class ManagerController extends Controller
{
    public function __construct(private ManagerService $managerService)
    {
    }

    /**
     * @param ManagerContactsRequest $request
     * @return Response
     * @throws AuthenticationException
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function show(ManagerContactsRequest $request): Response
    {
        $managers = $this->managerService->getManagerContacts($this->getAuthUser(), $request);

        return response()->json(new ManagersInfoCollection($managers));
    }
}
