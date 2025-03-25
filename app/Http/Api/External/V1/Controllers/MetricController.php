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
class MetricController extends Controller
{
    public function __construct()
    {
    }

    /**
     * @throws ValidationException
     */
    public function ping(): Response
    {
        return response()->json(['status' => 'pong']);
    }
}
