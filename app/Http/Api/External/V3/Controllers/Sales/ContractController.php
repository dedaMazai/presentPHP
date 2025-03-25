<?php

namespace App\Http\Api\External\V3\Controllers\Sales;

use App\Http\Api\External\V1\Controllers\Controller;
use App\Http\Resources\V3\Sales\Contract\DetailContractResource;
use App\Services\V3\Sales\ContractService;
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
    ) {
    }

    public function show(string $id)
    {
        $contract = $this->contractService->findContract($id, $this->getAuthUser());

        return response()->json(new DetailContractResource($contract));
    }
}
