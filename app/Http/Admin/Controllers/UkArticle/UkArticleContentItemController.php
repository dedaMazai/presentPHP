<?php

namespace App\Http\Admin\Controllers\UkArticle;

use App\Http\Admin\Controllers\Controller;
use App\Http\Admin\Requests\SaveContentItemRequest;
use App\Models\Article\Article;
use App\Models\ContentItem\ContentItem;
use App\Models\ContentItem\ContentItemType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Class UkArticleContentItemController
 *
 * @package App\Http\Admin\Controllers\Article
 */
class UkArticleContentItemController extends Controller
{
    public function sort(int $ukProjectId, Request $request, int $articleId): RedirectResponse
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer',
        ]);

        $this->findArticle($articleId)
            ->contentItems()
            ->setNewOrder($request->input('order'));

        return redirect()->back();
    }

    public function store(
        int $ukProjectId,
        SaveContentItemRequest $request,
        int $articleId,
        string $type
    ): RedirectResponse {
        /** @var ContentItem $item */
        $item = ContentItem::create([
            'type' => ContentItemType::from($type),
            'text' => $request->input('text'),
            'video_url' => $request->input('video_url'),
            'image_id' => $request->input('image_id'),
            'document_id' => $request->input('document_id'),
            'content' => $request->input('content'),
        ]);
        $this->findArticle($articleId)
            ->contentItems()
            ->save($item);

        if ($item->type->equals(ContentItemType::gallery())) {
            $item->images()->sync($request->input('gallery_image_ids', []));
        }

        return redirect()->back();
    }

    public function update(
        int $ukProjectId,
        SaveContentItemRequest $request,
        int $articleId,
        int $itemId
    ): RedirectResponse {
        /** @var ContentItem $item */
        $item = $this->findArticle($articleId)
            ->contentItems()
            ->findOrFail($itemId);
        $item->update($request->validated());

        if ($item->type->equals(ContentItemType::gallery())) {
            $item->images()->sync($request->input('gallery_image_ids', []));
        }

        return redirect()->back();
    }

    public function destroy(int $ukProjectId, int $articleId, int $itemId): RedirectResponse
    {
        $this->findArticle($articleId)
            ->contentItems()
            ->findOrFail($itemId)
            ->delete();

        return redirect()->back();
    }

    private function findArticle(int $id): Article
    {
        /** @var Article $article */
        $article = Article::withoutGlobalScope('published')->findOrFail($id);

        return $article;
    }
}
