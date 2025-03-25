<?php

namespace App\Http\Admin\Controllers\Settings;

use App\Http\Admin\Controllers\Controller;
use App\Services\Claim\ClaimCatalogueService;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use Illuminate\Http\RedirectResponse;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class SettingsCacheController
 *
 * @package App\Http\Admin\Controllers\Settings
 */
class SettingsCacheController extends Controller
{
    /**
     * @throws NotFoundException
     * @throws InvalidArgumentException
     * @throws BadRequestException
     */
    public function reloadCatalogueTreeCache(ClaimCatalogueService $catalogueService): RedirectResponse
    {
        $catalogueService->reloadTreeCache();
        $catalogueService->reloadPopularServicesCache();

        return redirect()->route('settings.edit');
    }
}
