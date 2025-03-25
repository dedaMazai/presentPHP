<?php

namespace App\Http\Api\External\V2\Controllers\Sales;

use App\Http\Api\External\V1\Requests\Sales\DeponentRequest;
use App\Http\Api\External\V2\Controllers\Controller;
use App\Http\Resources\V2\Sales\Deponent\DeponentResource;
use App\Services\Contract\ContractRepository;
use App\Services\Sales\Demand\DemandRepository;
use App\Services\V2\Sales\Demand\DemandService;
use Symfony\Component\HttpFoundation\Response;

class DeponentController extends Controller
{

    public function __construct(
        protected DemandService $demandService,
        protected DemandRepository $demandRepository,
        protected ContractRepository $contractRepository
    ) {
    }

    public function getDeponent(DeponentRequest $request): Response
    {
        $id = $request->get('id');
        $type = $request->get('type');
        $deponent = [];

        if ($type == 'demand') {
            $object = $this->demandRepository->getDemandById($id, $this->getAuthUser());
        } elseif ($type == 'contract') {
            $object = $this->contractRepository->getById($id);
        }

        if (isset($object)) {
            $deponent = $this->demandService->getDeponent($object, $id);
        }

        return response()->json(new DeponentResource([
            'depositor_info' => $deponent['depositorInfo'] ?? null,
            'depositor_document' => $deponent['depositorDocument'],
            'owner_id' => $deponent['ownerId'],
        ]));
    }
}
