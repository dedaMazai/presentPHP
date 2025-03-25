<?php

namespace App\Http\Admin\Controllers\Settings;

use App\Http\Admin\Controllers\Controller;
use App\Http\Admin\Requests\SaveSettingsDeletingReasonRequest;
use App\Models\User\DeletingReason;
use Illuminate\Http\RedirectResponse;

/**
 * Class SettingsDeletingReasonController
 *
 * @package App\Http\Admin\Controllers\Settings
 */
class SettingsDeletingReasonController extends Controller
{
    public function store(SaveSettingsDeletingReasonRequest $request): RedirectResponse
    {
        DeletingReason::create($request->validated());

        return redirect()->route('settings.edit');
    }

    public function update(SaveSettingsDeletingReasonRequest $request, int $id): RedirectResponse
    {
        $this->findReason($id)->update($request->validated());

        return redirect()->route('settings.edit');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->findReason($id)->delete();

        return redirect()->route('settings.edit');
    }

    private function findReason(int $id): DeletingReason
    {
        return DeletingReason::findOrFail($id);
    }
}
