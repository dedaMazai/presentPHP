<?php

namespace App\Http\Api\External\V2\Controllers\Sales;

use App\Http\Api\External\V1\Controllers\Controller;
use App\Http\Resources\Contract\ConfidantResource;
use App\Http\Resources\Sales\Contract\DetailUserArchiveContractResource;
use App\Http\Resources\V2\Sales\Contract\ContractJointOwnerInfoCollection;
use App\Http\Resources\V2\Sales\Contract\DetailContractResource;
use App\Http\Resources\V2\Sales\Contract\JointOwner\ContractJointOwnerResource;
use App\Services\Sales\Property\PropertyRepository;
use App\Services\V2\Sales\Contract\ContractSignInfoService;
use App\Services\V2\Sales\ContractService;
use App\Services\V2\Sales\Customer\CustomerContractJointOwnerInfoRepository;
use App\Services\V2\Sales\JointOwner\JointOwnerService;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ContractController
 *
 * @package App\Http\Api\External\V2\Controllers\Sales
 */
class ContractController extends Controller
{
    public function __construct(
        private ContractService $contractService,
        private CustomerContractJointOwnerInfoRepository $contractJointOwnerInfoRepository,
        private PropertyRepository $propertyRepository,
        private ContractSignInfoService $contractSignInfoService,
        private JointOwnerService $jointOwnerService
    ) {
    }
    public function show(string $id)
    {
        $contract = $this->contractService->findContract($id, $this->getAuthUser());

        return response()->json(new DetailContractResource($contract));
    }

    public function getConfidant(string $contractId, string $jointOwnersId)
    {
        $contract = $this->contractService->findContract($contractId, $this->getAuthUser());

        if ($contract != null) {
            $confidant = $this->contractService->getConfidant($contract, $jointOwnersId);
            return response()->json(new ConfidantResource($confidant));
        }

        return $this->empty();
    }

    public function getJointOwnersInfo(string $id): Response
    {
        $jointOwners = $this->contractService->getJointOwners($id);
        $contractJointOwners = [];

        foreach ($jointOwners as $jointOwner) {
            if ($jointOwner['roleCode']['code'] == 1) {
                $customer = $this->contractJointOwnerInfoRepository->getByCustomer($jointOwner, $this->getAuthUser());
                $contractJointOwners[] = $customer;
            }
        }

        return response()->json(new ContractJointOwnerInfoCollection($contractJointOwners));
    }

    public function getJointOwnersSignInfo(string $id): Response
    {
        $jointOwners = $this->contractService->getJointOwners($id);
        $dto = $this->contractSignInfoService->getJointOwnersSignInfo($jointOwners);

        return response()->json(new ContractJointOwnerResource($dto));
    }

    public function getArchiveContract(string $id)
    {
        $contract = $this->contractService->findContract($id, $this->getAuthUser());


        $jointOwners = $this->jointOwnerService->getJointOwners($contract->getJointOwners());
        $property = $this->propertyRepository->getById($contract->getArticleOrders()[0]->getPropertyId());

        return response()->json(new DetailUserArchiveContractResource(
            [
                'contract' => $contract,
                'jointOwners' => $jointOwners,
                'property' => $property,
            ]
        ));
    }
}
