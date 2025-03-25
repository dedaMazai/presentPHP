<?php

namespace App\Http\Api\External\V1\Controllers;

use App\Http\Api\External\V1\Requests\SaveMeterNameRequest;
use App\Http\Api\External\V1\Requests\SaveMeterValueRequest;
use App\Http\Resources\Meter\MeterCollection;
use App\Models\Meter\MeterSubtype;
use App\Models\Meter\MeterType;
use App\Services\Meter\Dto\MeterValueItemDto;
use App\Services\Meter\Dto\SaveMeterValueDto;
use App\Services\Meter\MeterRepository;
use App\Services\Meter\MeterService;
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
class MeterController extends Controller
{
    public function __construct(private MeterRepository $repository)
    {
    }

    /**
     * @throws ValidationException
     */
    public function index(string $accountNumber, Request $request): Response
    {
        $this->validate($request, [
            'type' => [
                Rule::in(MeterType::toValues()),
            ],
            'subtype' => [
                Rule::in(MeterSubtype::toValues()),
            ],
        ]);

        $meters = $this->repository->getAllByAccountNumber(
            $accountNumber,
            MeterType::tryFrom($request->input('type', '')),
            MeterSubtype::tryFrom($request->input('subtype', '')),
        );

        return response()->json(new MeterCollection($meters));
    }

    /**
     * @throws ValidationException
     */
    public function save(string $accountNumber, SaveMeterValueRequest $request, MeterService $service): Response
    {
        $itemDtos = [];
        foreach ($request->input('values') as $item) {
            $itemDtos[] = new MeterValueItemDto(
                tariffId: $item['tariff_id'],
                currentValue: $item['current_value'],
            );
        }

        $dto = new SaveMeterValueDto(
            id: $request->input('id'),
            values: $itemDtos,
        );
        $service->saveMeterValue($accountNumber, $dto);

        return $this->empty();
    }

    public function saveName(
        string $accountNumber,
        string $meterId,
        SaveMeterNameRequest $request,
        MeterService $service,
    ): Response {
        $service->saveMeterName($meterId, $request->input('name'));

        return $this->empty();
    }
}
