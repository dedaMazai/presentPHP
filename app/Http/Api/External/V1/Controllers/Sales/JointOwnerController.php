<?php

namespace App\Http\Api\External\V1\Controllers\Sales;

use App\Http\Api\External\V1\Requests\Auth\EmailCheckRequest;
use App\Http\Api\External\V1\Requests\Deal\StoreJointOwnerRequest;
use App\Http\Api\External\V1\Requests\Deal\UpdateJointOwnerRequest;
use App\Http\Resources\Sales\JointOwner\GetJointOwnersCollection;
use App\Services\Sales\JointOwner\Exceptions\EmailExistException;
use App\Services\Sales\JointOwner\Exceptions\PhoneExistException;
use Illuminate\Http\Request;
use App\Http\Resources\JointOwner\ParticipantsCollection;
use App\Http\Resources\JointOwner\StoreParticipantResource;
use App\Http\Resources\Sales\JointOwner\GetJointOwnerResourse;
use App\Http\Resources\Sales\JointOwner\JointOwnerResource;
use App\Models\Sales\OwnerType;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\Sales\Demand\DemandRepository;
use App\Services\Sales\Demand\DemandService;
use App\Services\Sales\JointOwner\Dto\StoreParticipantDto;
use App\Services\Sales\JointOwner\JointOwnerService;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MortgageController
 *
 * @package App\Http\Api\External\V1\Controllers\Sales
 */
class JointOwnerController extends BaseSalesController
{
    public function __construct(
        private DemandRepository $demandRepository,
        private JointOwnerService $jointOwnerService,
    ) {
        parent::__construct($this->demandRepository);
    }

    public function index($demandId): Response
    {
        $demand = $this->findDemand($demandId);

        $jointOwner = $this->jointOwnerService->getJointOwners($demand);

        return response()->json(new GetJointOwnersCollection($jointOwner));
    }

    public function show($demandId, $jointOwnerId): Response
    {
        $demand = $this->findDemand($demandId);

        $jointOwner = $this->jointOwnerService->getJointOwner($jointOwnerId, $demand, $this->getAuthUser());

        return response()->json(new GetJointOwnerResourse($jointOwner));
    }

    public function store(string $demandId, StoreJointOwnerRequest $request): Response
    {
        $demand = $this->findDemand($demandId);

        try {
            $data = $this->jointOwnerService->setJointOwnerLead($demand, $request, $this->getAuthUser());
        } catch (PhoneExistException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode());
        }

        return response()->json(new JointOwnerResource([
            'contactId' => $data['contactId'],
            'jointOwnerId' => $data['jointOwnerId']
        ]));
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function update(UpdateJointOwnerRequest $request, string $demandId, string $jointownerId): Response
    {
        $this->findDemand($demandId);

        try {
            $jointOwner = $this->jointOwnerService->updateJointOwner($demandId, $jointownerId, $request);
        } catch (PhoneExistException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode());
        }

        return response()->json(new JointOwnerResource($jointOwner));
    }

    public function storeCustomer($demandId, $customerId): Response
    {
        $this->findDemand($demandId);

        $customer = $this->jointOwnerService->setJointOwnerCustomer($demandId, $customerId);

        return response()->json(new JointOwnerResource($customer));
    }

    public function storeParticipants(Request $request, $jointOwnerId, $demandid): Response
    {
        $jointOwners = $this->jointOwnerService->getParticipant(
            $request->owner_code,
            $jointOwnerId,
            $demandid,
            $this->getAuthUser()
        );


        if ($jointOwners == []) {
            return response()->json([
                'id' => [],
                'type_message' => 1,
                'message' => 'Список участников пуст',
            ]);
        }

        return response()->json([
            'id' => new ParticipantsCollection($jointOwners),
            'type_message' => 1,
            'message' => '',
        ]);
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function destroy(string $demandId, string $jointownerId): Response
    {
        $this->findDemand($demandId);

        $this->jointOwnerService->deleteJointOwner($demandId, $jointownerId);

        return $this->response();
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function emailCheck(EmailCheckRequest $request): Response
    {
        $isUniqueEmail = $this->jointOwnerService->emailCheck($request->email);

        return $this->response(['is_unique_email' => $isUniqueEmail]);
    }
}
