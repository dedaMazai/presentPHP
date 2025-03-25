<?php

namespace App\Http\Admin\Controllers\News;

use App\Http\Admin\Controllers\Controller;
use App\Http\Admin\Requests\SaveContentItemRequest;
use App\Models\ContentItem\ContentItem;
use App\Models\ContentItem\ContentItemType;
use App\Models\News\News;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Class NewsContentItemController
 *
 * @package App\Http\Admin\Controllers\News
 */
class NewsContentItemController extends Controller
{
    public function sort(Request $request, int $newsId): RedirectResponse
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer',
        ]);

        $news = $this->findNews($newsId);
        $news->contentItems()->setNewOrder($request->input('order'));

        return redirect()->back();
    }

    public function store(SaveContentItemRequest $request, int $newsId, string $type): RedirectResponse
    {
        $news = $this->findNews($newsId);
        /** @var ContentItem $item */

        $item = ContentItem::create([
            'type' => ContentItemType::from($type),
            'text' => $request->input('text'),
            'video_url' => $request->input('video_url'),
            'image_id' => $request->input('image_id'),
            'document_id' => $request->input('document_id'),
            'content' => $request->input('content'),
        ]);
        $news->contentItems()->save($item);

        if ($item->type->equals(ContentItemType::gallery())) {
            $item->images()->sync($request->input('gallery_image_ids', []));
        }

        return redirect()->back();
    }

    public function update(SaveContentItemRequest $request, int $newsId, int $itemId): RedirectResponse
    {
        $news = $this->findNews($newsId);
        /** @var ContentItem $item */
        $item = $news->contentItems()->findOrFail($itemId);
        $item->update($request->validated());

        if ($item->type->equals(ContentItemType::gallery())) {
            $item->images()->sync($request->input('gallery_image_ids', []));
        }

        return redirect()->back();
    }

    public function destroy(int $newsId, int $itemId): RedirectResponse
    {
        $news = $this->findNews($newsId);
        $news->contentItems()->findOrFail($itemId)->delete();

        return redirect()->back();
    }

    private function findNews(int $id): News
    {
        return News::withoutGlobalScope('published')->findOrFail($id);
    }
}
