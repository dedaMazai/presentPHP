<?php

namespace App\Http\Api\External\V1\Controllers\Sales;

use App\Http\Api\External\V1\Requests\Sales\Inspection\CreateInspectionRequest;
use App\Http\Api\External\V1\Requests\Sales\Inspection\UpdateInspectionRequest;
use App\Services\Sales\Demand\DemandRepository;
use App\Services\Sales\Inspection\InspectionService;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class InspectionController
 *
 * @package App\Http\Api\External\V1\Controllers\Sales
 */
class InspectionController extends BaseSalesController
{
    public function __construct(
        private DemandRepository  $demandRepository,
        private InspectionService $inspectionService,
    ) {
        parent::__construct($this->demandRepository);
    }


    /**
     * @param string $articleId
     * @return Response
     */
    public function getInspectionDate(string $articleId): Response
    {
        $date = $this->inspectionService->inspectionDate($articleId);

        return response()->json($date);
    }


    /**
     * @param string $inspectionId
     * @return Response
     */
    public function delInspection(string $inspectionId): Response
    {
        $this->inspectionService->deleteInspection($inspectionId);

        return $this->empty();
    }

    /**
     * @param string $articleId
     * @return Response
     */
    public function getInspection(string $articleId): Response
    {
        $data = $this->inspectionService->getInspection($articleId);

        return response()->json($data);
    }

    /**
     * @param string $articleId
     * @return Response
     */
    public function createInspection(CreateInspectionRequest $request): Response
    {
        $this->inspectionService->createInspection($request);

        return $this->empty();
    }

    /**
     * @param string $articleId
     * @param string $date
     * @return Response
     */
    public function getDateTimes(string $articleId, string $date): Response
    {
        $times = $this->inspectionService->dateTimes($articleId, $date);

        return response()->json($times);
    }

    /**
     * @param string $inspectionId
     * @return Response
     */
    public function updateInspection(UpdateInspectionRequest $request, string $inspectionId): Response
    {
        $this->inspectionService->updateInspection($request, $inspectionId);

        return $this->empty();
    }
}
