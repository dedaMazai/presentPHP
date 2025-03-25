<?php

namespace App\Http\Api\External\V2\Controllers\Sales;

use App\Http\Api\External\V1\Controllers\Controller;
use App\Models\Sales\Deal;
use App\Models\V2\Sales\Demand\Demand;
use App\Services\V2\Sales\Demand\DemandRepository;
use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class BaseSalesController
 *
 * @package App\Http\Api\External\V2\Controllers\Sales
 */
abstract class BaseSalesController extends Controller
{
    public function __construct(private DemandRepository $demandRepository)
    {
    }

    protected function findDemand(string $id): Demand
    {
        try {
            return $this->demandRepository->getDemandById($id, $this->getAuthUser());
        } catch (Exception) {
            throw new NotFoundHttpException('Demand not found.');
        }
    }

    protected function findDeal(string $demandId): Deal
    {
        $deal = Deal::firstWhere(['demand_id' => $demandId]);
        if ($deal === null) {
            throw new NotFoundHttpException('Deal not found.');
        }

        return $deal;
    }

    protected function findDemandJointOwners(string $id)
    {
        try {
            return $this->demandRepository->getDemandJointOwnersById($id, $this->getAuthUser());
        } catch (Exception $e) {
            throw new NotFoundHttpException($e->getMessage());
        }
    }
}
