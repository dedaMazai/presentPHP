<?php

namespace App\Http\Admin\Controllers\Settings;

use App\Http\Admin\Controllers\Controller;
use App\Http\Admin\Requests\SaveSettingsDocumentsRequest;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;

/**
 * Class SettingsDocumentsController
 *
 * @package App\Http\Admin\Controllers\Settings
 */
class SettingsDocumentsController extends Controller
{
    public function update(SaveSettingsDocumentsRequest $request, SettingsService $service): RedirectResponse
    {
        $service->updateDocuments(
            $request->input('offer_url'),
            $request->input('consent_url'),
            $request->input('confidant_url'),
        );

        return redirect()->route('settings.edit');
    }
}
