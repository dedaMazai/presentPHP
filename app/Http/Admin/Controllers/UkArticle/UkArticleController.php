<?php

namespace App\Http\Admin\Controllers\UkArticle;

use App\Components\QueryBuilder\Filters\FiltersCreatedBetween;
use App\Http\Admin\Controllers\Controller;
use App\Http\Admin\Requests\Article\SaveUkArticleRequest;
use App\Models\Article\Article;
use App\Models\UkProject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class UkArticleController
 *
 * @package App\Http\Admin\Controllers\UkArticle
 */
class UkArticleController extends Controller
{
    public function index(int $ukProjectId, Request $request): Response
    {
        $ukProject = $this->findUkProject($ukProjectId);

        return inertia('UkProjects/Articles/List', [
            'articles' => QueryBuilder::for(Article::class)
                ->withoutGlobalScope('published')
                ->byUkProject($ukProject, true)
                ->allowedFilters([
                    AllowedFilter::exact('is_published'),
                    'title',
                    AllowedFilter::custom('creation_period', new FiltersCreatedBetween()),
                ])
                ->allowedSorts(['is_published', 'created_at', 'updated_at'])
                ->with('iconImage')
                ->paginate(),
            'defaultSorter' => $request->input('sort'),
            'defaultFilters' => $request->input('filter'),
            'ukProject' => $ukProject,
        ]);
    }

    public function create(int $ukProjectId): Response
    {
        return inertia('UkProjects/Articles/Create', [
            'ukProject' => $this->findUkProject($ukProjectId),
        ]);
    }

    public function store(int $ukProjectId, SaveUkArticleRequest $request): RedirectResponse
    {
        /** @var Article $article */
        $article = $this->findUkProject($ukProjectId)
            ->articles()
            ->create($request->validated());

        return redirect()->route('uk-projects.articles.edit', [
            'ukProjectId' => $ukProjectId,
            'id' => $article->id,
        ]);
    }

    public function edit(int $ukProjectId, int $id): Response
    {
        $article = $this->findArticle($id);

        return inertia('UkProjects/Articles/Edit', [
            'article' => $article,
            'contentItems' => $article->contentItems()
                ->with(['image', 'images', 'document', 'documents'])->get()->all(),
            'ukProject' => $this->findUkProject($ukProjectId),
        ]);
    }

    public function update(
        int $ukProjectId,
        SaveUkArticleRequest $request,
        int $id
    ): RedirectResponse {
        $this->findArticle($id)
            ->update($request->validated());

        return redirect()->route('uk-projects.articles.edit', [
            'ukProjectId' => $ukProjectId,
            'id' => $id,
        ]);
    }

    public function updateStatus(
        int $ukProjectId,
        Request $request,
        int $id
    ): RedirectResponse {
        $request->validate([
            'is_published' => 'required|boolean',
        ]);

        $this->findArticle($id)
            ->updatePublicationStatus($request->input('is_published'));

        return redirect()->back();
    }

    public function destroy(int $ukProjectId, int $id): RedirectResponse
    {
        $this->findArticle($id)->delete();

        return redirect()->route('uk-projects.articles', [
            'ukProjectId' => $ukProjectId,
        ]);
    }

    public function sort(Request $request): RedirectResponse
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer',
        ]);

        Article::setNewOrder($request->input('order'));

        return redirect()->back();
    }

    private function findUkProject(int $id): UkProject
    {
        return UkProject::withoutGlobalScope('published')
            ->with('image')
            ->findOrFail($id);
    }

    private function findArticle(int $id): Article
    {
        /** @var Article $article */
        $article = Article::withoutGlobalScope('published')
            ->with('iconImage')
            ->findOrFail($id);

        return $article;
    }
}
