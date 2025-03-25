<?php

namespace App\Http\Admin\Controllers\Settings;

use App\Http\Admin\Controllers\Controller;
use App\Http\Admin\Requests\SaveSettingsBuildsRequest;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;

/**
 * Class SettingsBuildsController
 *
 * @package App\Http\Admin\Controllers\Settings
 */
class SettingsBuildsController extends Controller
{
    public function update(SaveSettingsBuildsRequest $request, SettingsService $service): RedirectResponse
    {
        $service->updateBuilds(
            $request->input('build_android_url'),
            $request->input('build_ios_url'),
        );

        return redirect()->route('settings.edit');
    }
}
