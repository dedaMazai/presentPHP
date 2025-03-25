<?php

namespace App\Http\Admin\Controllers\Article;

use App\Http\Admin\Controllers\Controller;
use App\Http\Admin\Requests\SaveContentItemRequest;
use App\Models\Article\Article;
use App\Models\ContentItem\ContentItem;
use App\Models\ContentItem\ContentItemType;
use App\Models\Project\ProjectType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Class ArticleContentItemController
 *
 * @package App\Http\Admin\Controllers\Article
 */
class ArticleContentItemController extends Controller
{
    public function sort(ProjectType $projectType, int $projectId, Request $request, int $articleId): RedirectResponse
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
        ProjectType $projectType,
        int $projectId,
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
        ProjectType $projectType,
        int $projectId,
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

    public function destroy(ProjectType $projectType, int $projectId, int $articleId, int $itemId): RedirectResponse
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
