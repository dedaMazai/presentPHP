<?php

namespace App\Http\Api\External\V1\Controllers;

use App\Http\Resources\BannerCollection;
use App\Models\Banner\Banner;
use App\Models\Banner\BannerPlace;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BannerController
 *
 * @package App\Http\Api\External\V1\Controllers
 */
class BannerController extends Controller
{
    public function show(string $place): Response
    {
        return response()->json(new BannerCollection(Banner::byPlace(BannerPlace::from($place))->byActive()->get()));
    }
}
