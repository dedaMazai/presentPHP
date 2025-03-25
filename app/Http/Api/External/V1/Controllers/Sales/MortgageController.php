<?php

namespace App\Http\Api\External\V1\Controllers\Sales;

use App\Http\Resources\Sales\MortgageApprovalCollection;
use App\Models\Sales\Demand\DemandType;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\Sales\Deal\DealService;
use App\Services\Sales\Demand\DemandRepository;
use App\Services\Sales\Demand\DemandService;
use App\Services\Sales\MortgageApprovalRepository;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MortgageController
 *
 * @package App\Http\Api\External\V1\Controllers\Sales
 */
class MortgageController extends BaseSalesController
{
    public function __construct(
        private DemandRepository $demandRepository,
        private MortgageApprovalRepository $mortgageApprovalRepository,
        private DemandService $demandService,
        private DealService $dealService,
    ) {
        parent::__construct($this->demandRepository);
    }

    /**
     * @throws BadRequestException
     * @throws AuthenticationException
     * @throws NotFoundException
     */
    public function createMortgageDemand(string $id): Response
    {
        $demand = $this->findDemand($id);
        if ($demand->getMortgageDemand()) {
            $mortgageDemandId = $demand->getMortgageDemand()->getId();
        } else {
            $mortgageDemandId = null;
            $childDemands = $this->demandRepository->getDemands($this->getAuthUser(), $demand->getId());
            foreach ($childDemands as $childDemand) {
                if ($childDemand->getType()->equals(DemandType::mortgage())) {
                    $this->dealService->setMortgageDemandId($demand->getDeal(), $childDemand->getId());
                    $mortgageDemandId = $childDemand->getId();
                }
            }
        }

        if (!$mortgageDemandId) {
            $mortgageDemandId = $this->demandService->createMortgageDemand($demand, $this->getAuthUser());
        }

        return $this->response(['mortgage_demand_id' => $mortgageDemandId]);
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getMortgageUrl(string $id): Response
    {
        $demand = $this->findDemand($id);

        $mortgageUrl = $this->demandService->getMortgageUrl($demand->getId());

        return $this->response(['url' => $mortgageUrl]);
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getMortgageApprovals(string $id): Response
    {
        $demand = $this->findDemand($id);

        $mortgageApprovals = $this->mortgageApprovalRepository->getAllByDemand($demand);

        return response()->json(new MortgageApprovalCollection($mortgageApprovals));
    }
}
