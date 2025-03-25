<?php

namespace App\Http\Api\External\V1\Controllers;

use App\Http\Resources\Meter\MetersCheckResource;
use App\Http\Resources\Meter\MeterStatisticsResource;
use App\Http\Resources\Meter\MeterTypeCollection;
use App\Models\Meter\MeterSubtype;
use App\Models\Meter\MeterType;
use App\Services\Meter\MeterStatisticsRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MeterController
 *
 * @package App\Http\Api\External\V1\Controllers
 */
class MeterStatisticsController extends Controller
{
    public function __construct(private MeterStatisticsRepository $repository)
    {
    }

    /**
     * @throws ValidationException
     */
    public function index(string $accountNumber, Request $request): Response
    {
        $this->validate($request, [
            'type' => [
                'required',
                Rule::in(MeterType::toValues()),
            ],
            'subtype' => [
                'nullable',
                Rule::in(MeterSubtype::toValues()),
            ],
            'year' => 'required|integer',
        ]);

        $startDate = Carbon::createFromDate($request->input('year'), 1, 1);
        $endDate = $startDate->clone()->endOfYear();

        $meterStatistics = $this->repository->getByAccountNumberAndType(
            $accountNumber,
            MeterType::from($request->input('type')),
            MeterSubtype::tryFrom($request->input('subtype')),
            $startDate,
            $endDate,
        );

        return response()->json(new MeterStatisticsResource($meterStatistics));
    }

    public function check(string $accountNumber): Response
    {
        $meters = $this->repository->checkMeters($accountNumber);

        return response()->json(new MetersCheckResource($meters));
    }

    public function getStatisticType(string $accountNumber, Request $request): Response
    {
        $this->validate($request, [
            'year' => 'required',
        ]);

        $types = [];
        $subtypes = [];
        $response = [];
        $neededSubtypes = [];

        $statisticType = $this->repository->statisticType($accountNumber, $request->year);

        foreach ($statisticType as $item) {
            if (isset($item['type'])) {
                $types[] = $item['type'];
            }
            if (isset($item['subtype'])) {
                $subtypes[] = $item['subtype'];
            }
        }

        $uniqueTypes = array_unique($types);
        $uniqueSubtypes = array_unique($subtypes);

        $expectedTypes = ['heat', 'electricity'];
        $expectedSubtypes = ['hot', 'cold', 'pure'];

        foreach ($expectedTypes as $type) {
            if (in_array($type, $uniqueTypes)) {
                $response[] = ['type' => $type];
            }
        }

        if (in_array('water', $uniqueTypes)) {
            foreach ($expectedSubtypes as $subtype) {
                if (in_array($subtype, $uniqueSubtypes)) {
                    $neededSubtypes[] = $subtype;
                }
            }
            $response[] = ['type' => 'water', 'subtype' => $neededSubtypes];
        }

        return response()->json(new MeterTypeCollection($response));
    }
}
