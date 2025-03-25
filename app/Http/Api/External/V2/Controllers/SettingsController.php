<?php

namespace App\Http\Api\External\V2\Controllers;

use App\Http\Api\External\V1\Controllers\Controller;
use App\Http\Resources\ContentItemCollection;
use App\Http\Resources\GeneralSettingsResource;
use App\Models\Settings;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class SettingsController
 *
 * @package App\Http\Api\Exeternal\V1\Controllers
 */
class SettingsController extends Controller
{
    public function showGeneral(): Response
    {
        return response()->json(new GeneralSettingsResource($this->findSettings()));
    }

    public function showAboutCompany(): Response
    {
        return response()->json(["content_items" => new ContentItemCollection($this->findSettings()->contentItems)]);
    }

    private function findSettings(): Settings
    {
        /* @var Settings $settings */
        $settings = Settings::first();
        if ($settings === null) {
            throw new NotFoundHttpException('Settings not found.');
        }

        return $settings;
    }
}
