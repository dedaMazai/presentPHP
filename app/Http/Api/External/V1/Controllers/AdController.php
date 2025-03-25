<?php

namespace App\Http\Api\External\V1\Controllers;

use App\Http\Resources\AdResource;
use App\Models\Ad\Ad;
use App\Models\Ad\AdPlace;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AdController
 *
 * @package App\Http\Api\External\V1\Controllers
 */
class AdController extends Controller
{
    public function show(string $place): Response
    {
        $ad = Ad::byActive()->byPlace(AdPlace::from($place))->orderBy('created_at')->first();
        if ($ad === null) {
            return $this->empty();
        }

        return response()->json(new AdResource($ad));
    }
}
