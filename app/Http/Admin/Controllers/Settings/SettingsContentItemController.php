<?php

namespace App\Http\Admin\Controllers\Settings;

use App\Http\Admin\Controllers\Controller;
use App\Http\Admin\Requests\SaveContentItemRequest;
use App\Models\ContentItem\ContentItem;
use App\Models\ContentItem\ContentItemType;
use App\Models\Settings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Class SettingsContentItemController
 *
 * @package App\Http\Admin\Controllers\Settings
 */
class SettingsContentItemController extends Controller
{
    public function sort(Request $request): RedirectResponse
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer',
        ]);

        /** @var Settings $settings */
        $settings = Settings::first();
        $settings->contentItems()->setNewOrder($request->input('order'));

        return redirect()->back();
    }

    public function store(SaveContentItemRequest $request, string $type): RedirectResponse
    {
        /** @var Settings $settings */
        $settings = Settings::first();

        /** @var ContentItem $item */
        $item = ContentItem::create([
            'type' => ContentItemType::from($type),
            'text' => $request->input('text'),
            'video_url' => $request->input('video_url'),
            'image_id' => $request->input('image_id'),
            'content' => $request->input('content'),
        ]);
        $settings->contentItems()->save($item);

        if ($item->type->equals(ContentItemType::gallery())) {
            $item->images()->sync($request->input('gallery_image_ids', []));
        }

        return redirect()->back();
    }

    public function update(SaveContentItemRequest $request, int $itemId): RedirectResponse
    {
        /** @var Settings $settings */
        $settings = Settings::first();

        /** @var ContentItem $item */
        $item = $settings->contentItems()->findOrFail($itemId);
        $item->update($request->validated());

        if ($item->type->equals(ContentItemType::gallery())) {
            $item->images()->sync($request->input('gallery_image_ids', []));
        }

        return redirect()->back();
    }

    public function destroy(int $itemId): RedirectResponse
    {
        /** @var Settings $settings */
        $settings = Settings::first();
        $settings->contentItems()->findOrFail($itemId)->delete();

        return redirect()->back();
    }
}
