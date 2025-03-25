<?php

namespace App\Http\Admin\Controllers\News;

use App\Components\QueryBuilder\Filters\FiltersCreatedBetween;
use App\Http\Admin\Controllers\Controller;
use App\Http\Admin\Requests\SaveNewsRequest;
use App\Models\Building\Building;
use App\Models\ContentItem\ContentItem;
use App\Models\News\News;
use App\Models\News\NewsCategory;
use App\Models\News\NewsType;
use App\Models\UkProject;
use App\Services\Notification\DestinationTypeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class NewsController
 *
 * @package App\Http\Admin\Controllers\News
 */
class NewsController extends Controller
{
    public function index(Request $request): Response
    {
        return inertia('News/List', [
            'news' => QueryBuilder::for(News::class)
                ->withoutGlobalScope('published')
                ->allowedFilters([
                    AllowedFilter::exact('is_published'),
                    AllowedFilter::exact('type'),
                    AllowedFilter::exact('category'),
                    AllowedFilter::exact('uk_project_id'),
                    'title',
                    'tag',
                    AllowedFilter::custom('creation_period', new FiltersCreatedBetween()),
                ])
                ->allowedSorts(['is_published', 'created_at', 'updated_at'])
                ->with('previewImage')
                ->latest()
                ->paginate(),
            'defaultSorter' => $request->input('sort'),
            'defaultFilters' => $request->input('filter'),
            'types' => NewsType::toArray(),
            'categories' => NewsCategory::toArray(),
        ]);
    }

    public function create(
        DestinationTypeService $destinationTypeService,
        Request $request
    ): Response {
        $destinations = $destinationTypeService->getAll();

        $newsCopy = null;
        if ($request->input('copied')) {
            $newsCopy = session('temp_news_copy', null);
        }
        return inertia('News/Create', [
            'types'        => NewsType::toArray(),
            'categories'   => NewsCategory::toArray(),
            'destinations' => $destinations,
            'ukProjects'   => UkProject::all(),
            'ukBuildings'  => Building::all(),
            'newsCopy'     => $newsCopy,
        ]);
    }

    public function store(SaveNewsRequest $request): RedirectResponse
    {
        /** @var News $news */;
        $news = News::create($request->validated());

        return redirect()->route('news.edit', [
            'id' => $news->id,
        ]);
    }

    public function edit(
        int $id,
        DestinationTypeService $destinationTypeService
    ): Response {
        $news = $this->findNews($id);
        $destinations = $destinationTypeService->getAll();

        return inertia('News/Edit', [
            'news' => $news,
            'types' => NewsType::toArray(),
            'categories' => NewsCategory::toArray(),
            'destinations' => $destinations,
            'ukProjects' => UkProject::all(),
            'ukBuildings' => Building::all(),
            'contentItems' => $news->contentItems()->with(['image', 'images', 'document', 'documents'])->get()->all(),
            'isSent' => $news->is_sent,
        ]);
    }

    public function update(SaveNewsRequest $request, int $id): RedirectResponse
    {
        $news = $this->findNews($id);
        $news->update($request->validated());

        return redirect()->route('news.edit', [
            'id' => $news->id,
        ]);
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'is_published' => 'required|boolean',
        ]);

        $this->findNews($id)->updatePublicationStatus($request->input('is_published'));

        return redirect()->back();
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->findNews($id)->delete();

        return redirect()->route('news');
    }

    private function findNews(int $id): News
    {
        return News::withoutGlobalScope('published')
            ->with('previewImage')
            ->findOrFail($id);
    }


    public function copy(int $id)
    {
        DB::beginTransaction();
        try {
            $originalNews = News::query()
                ->withoutGlobalScope('published')
                ->findOrFail($id);

            $originalNews->load(['previewImage', 'ukProject', 'contentItems']);

            $copiedNews = $originalNews->replicate();
            $copiedNews->is_published = false;
            $copiedNews->is_sent = false;
            $copiedNews->should_send_notification = false;
            $copiedNews->count = 0;
            $copiedNews->save();
            $contentItemIds = $originalNews->contentItems->pluck('id')->toArray();
            if (isset($contentItemIds)) {
                foreach ($contentItemIds as $contentItemId) {
                    $originalContentItem = ContentItem::query()->findOrFail($contentItemId);
                    $copiedContentItem = $originalContentItem->replicate();
                    $copiedContentItem->save();

                    $copiedNews
                        ->contentItems()
                        ->attach($copiedContentItem->id, [
                            'order' => $originalContentItem->order
                        ]);
                }
            }


            DB::commit();

            return redirect()
                ->route('news.edit', $copiedNews->id)
                ->with('success', 'Данные для копирования загружены');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Ошибка копирования: ' . $e->getMessage());
        }
    }

//    public function storeFromEdit(SaveNewsRequest $request, int $id)
//    {
//        $news = News::create($request->validated());
//        return redirect()->route('news')
//            ->with('success', 'Новость сохранена как новая!');
//    }
}
