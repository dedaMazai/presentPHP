<?php

namespace App\Http\Api\External\V2\Controllers\Sales;

use App\Http\Resources\V2\Sales\JointOwner\GetJointOwnersCollection;
use App\Http\Resources\V2\Sales\JointOwner\GetJointOwnersResource;
use App\Services\V2\Sales\Demand\DemandRepository;
use App\Services\V2\Sales\JointOwner\JointOwnerService;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MortgageController
 *
 * @package App\Http\Api\External\V2\Controllers\Sales
 */
class JointOwnerController extends BaseSalesController
{
    public function __construct(
        private DemandRepository $demandRepository,
        private JointOwnerService $jointOwnerService,
    ) {
        parent::__construct($this->demandRepository);
    }

    public function index($demandId)
    {
        $demand = $this->findDemandJointOwners($demandId);

        $jointOwner = $this->jointOwnerService->getJointOwners($demand);

        return new GetJointOwnersCollection($jointOwner);
    }
}
