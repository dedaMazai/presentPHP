<?php

namespace App\Http\Api\External\V1\Controllers\IndividualOwner;

use App\Http\Api\External\V1\Controllers\Controller;
use App\Http\Api\External\V1\Requests\Sales\IndividualOwner\UpdateIndividualOwnerRequest;
use App\Http\Resources\IndividualOwner\GetIndividualOwnerResource;
use App\Services\DynamicsCrm\DynamicsCrmClient;

use App\Services\Sales\IndividualOwner\IndividualOwnerService;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class IndividualOwner
 *
 * @package App\Http\Api\External\V1\Controllers\IndividualOwner
 */

class IndividualOwnerController extends Controller
{
    public function __construct(
        protected DynamicsCrmClient $dynamicsCrmClient,
        protected IndividualOwnerService $individualOwnerService
    ) {
    }

    /**
     * @throws AuthenticationException
     */
    public function getInfo(): Response
    {
        $user = $this->getAuthUser();
        $customer = $this->individualOwnerService->findIndividualOwnerInfo($user);

        return response()->json(new GetIndividualOwnerResource($customer));
    }

    public function setInfo(UpdateIndividualOwnerRequest $request): Response
    {
        $user = $this->getAuthUser();
        $data = $request->validated();
        $this->individualOwnerService->updateIndividualOwnerInfo($user, $data);
        return response()->json('successful', Response::HTTP_NO_CONTENT);
    }
}
