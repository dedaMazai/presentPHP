<?php

namespace App\Http\Admin\Controllers\Settings;

use App\Http\Admin\Controllers\Controller;
use App\Http\Admin\Requests\SaveSettingsContactRequest;
use App\Models\Contact\Contact;
use App\Models\Settings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Class SettingsContactController
 *
 * @package App\Http\Admin\Controllers\Settings
 */
class SettingsContactController extends Controller
{
    public function store(SaveSettingsContactRequest $request): RedirectResponse
    {
        Settings::first()->contacts()->create($request->validated());

        return redirect()->route('settings.edit');
    }

    public function update(SaveSettingsContactRequest $request, int $id): RedirectResponse
    {
        $this->findContact($id)->update($request->validated());

        return redirect()->route('settings.edit');
    }

    private function findContact(int $id): Contact
    {
        return Contact::with(['iconImage'])->findOrFail($id);
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->findContact($id)->delete();

        return redirect()->route('settings.edit');
    }

    public function sort(Request $request): RedirectResponse
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer',
        ]);

        Contact::setNewOrder($request->input('order'));

        return redirect()->back();
    }
}
