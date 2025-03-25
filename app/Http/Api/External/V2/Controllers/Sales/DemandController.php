<?php

namespace App\Http\Api\External\V2\Controllers\Sales;

use App\Http\Resources\V2\Sales\Demand\DetailDemandResource;
use App\Http\Resources\V2\Sales\Contract\ContractCollection;
use App\Http\Resources\V2\Sales\Demand\DemandCollection;
use App\Services\V2\Contract\ContractRepository;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\V2\Sales\Demand\DemandRepository;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DemandController
 *
 * @package App\Http\Api\External\V2\Controllers\Sales
 */
class DemandController extends BaseSalesController
{
    public function __construct(
        private DemandRepository $demandRepository,
        private ContractRepository $contractRepository,
    ) {
        parent::__construct($this->demandRepository);
    }

    /**
     * @throws AuthenticationException
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function index(): Response
    {
        $demands = $this->demandRepository->getDemands($this->getAuthUser());
        $contracts = $this->contractRepository->getDemandContracts($this->getAuthUser()->crm_id);

        $demandCollection = new DemandCollection($demands);
        $contractCollection = new ContractCollection($contracts);

        return response()->json($demandCollection->merge($contractCollection));
    }

    public function show(string $id): Response
    {
        $demand = $this->findDemand($id);

        return response()->json(new DetailDemandResource($demand));
    }
}
