<?php

namespace App\Http\Api\External\V1\Controllers;

use App\Http\Resources\CityCollection;
use App\Models\City;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CityController
 *
 * @package App\Http\Api\External\V1\Controllers
 */
class CityController extends Controller
{
    public function index(): Response
    {
        return response()->json(new CityCollection(City::all()));
    }
}
