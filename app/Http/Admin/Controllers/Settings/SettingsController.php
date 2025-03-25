<?php

namespace App\Http\Admin\Controllers\Settings;

use App\Http\Admin\Controllers\Controller;
use App\Http\Admin\Requests\SaveSettingsRequest;
use App\Models\City;
use App\Models\Contact\ContactType;
use App\Models\Settings;
use App\Models\User\DeletingReason;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

/**
 * Class SettingsController
 *
 * @package App\Http\Admin\Controllers\Settings
 */
class SettingsController extends Controller
{
    public function edit(): Response
    {
        $settings = Settings::firstOrCreate();

        return inertia('Settings/Edit', [
            'settings' => $settings,
            'contentItems' => $settings?->contentItems()->with(['image', 'images'])->get()->all(),
            'contacts' => $settings->contacts()->with(['iconImage'])->get(),
            'contactTypes' => ContactType::toArray(),
            'cities' => City::all(),
            'deletingReasons' => DeletingReason::all(),
        ]);
    }

    public function update(SaveSettingsRequest $request): RedirectResponse
    {
        /** @var Settings $settings */
        $settings = Settings::first();
        $settings->update($request->validated());

        return redirect()->route('settings.edit');
    }
}
