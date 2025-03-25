<?php

namespace App\Http\Admin\Controllers\Settings;

use App\Http\Admin\Controllers\Controller;
use App\Http\Admin\Requests\SaveServicesSettingsRequest;
use App\Models\Settings;
use Illuminate\Http\RedirectResponse;

/**
 * Class SettingsServicesController
 *
 * @package App\Http\Admin\Controllers\Settings
 */
class SettingsServicesController extends Controller
{
    public function update(SaveServicesSettingsRequest $request): RedirectResponse
    {
        /** @var Settings $settings */
        $settings = Settings::first();
        $settings->update($request->validated());

        return redirect()->route('settings.edit');
    }
}
