<?php

namespace App\Http\Api\External\V1\Controllers;

use App\Http\Resources\Meter\MeterTariffCollection;
use App\Services\Meter\MeterTariffRepository;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MeterTariffController
 *
 * @package App\Http\Api\External\V1\Controllers
 */
class MeterTariffController extends Controller
{
    public function __construct(private MeterTariffRepository $repository)
    {
    }

    /**
     * @throws ValidationException
     */
    public function index(string $accountNumber): Response
    {
        $meterTariffs = $this->repository->getAllByAccountNumber($accountNumber);

        return response()->json(new MeterTariffCollection(array_values($meterTariffs)));
    }
}
