<?php

namespace App\Http\Admin\Controllers\Article;

use App\Components\QueryBuilder\Filters\FiltersCreatedBetween;
use App\Http\Admin\Controllers\Controller;
use App\Http\Admin\Requests\Article\SaveArticleRequest;
use App\Models\Article\Article;
use App\Models\Project\Project;
use App\Models\Project\ProjectType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class ArticleController
 *
 * @package App\Http\Admin\Controllers\Article
 */
class ArticleController extends Controller
{
    public function index(ProjectType $projectType, int $projectId, Request $request): Response
    {
        $project = $this->findProject($projectType, $projectId);

        return inertia('Projects/Articles/List', [
            'articles' => QueryBuilder::for(Article::class)
                ->withoutGlobalScope('published')
                ->byProject($project, true)
                ->allowedFilters([
                    AllowedFilter::exact('is_published'),
                    'title',
                    AllowedFilter::custom('creation_period', new FiltersCreatedBetween()),
                ])
                ->allowedSorts(['is_published', 'created_at', 'updated_at'])
                ->paginate(),
            'defaultSorter' => $request->input('sort'),
            'defaultFilters' => $request->input('filter'),
            'project' => $project,
            'type' => $projectType,
        ]);
    }

    public function create(ProjectType $projectType, int $projectId): Response
    {
        return inertia('Projects/Articles/Create', [
            'project' => $this->findProject($projectType, $projectId),
            'type' => $projectType,
        ]);
    }

    public function store(ProjectType $projectType, int $projectId, SaveArticleRequest $request): RedirectResponse
    {
        /** @var Article $article */
        $article = $this->findProject($projectType, $projectId)
            ->articles()
            ->create($request->validated());

        return redirect()->route('projects.articles.edit', [
            'projectType' => $projectType->id,
            'projectId' => $projectId,
            'id' => $article->id,
        ]);
    }

    public function edit(ProjectType $projectType, int $projectId, int $id): Response
    {
        $article = $this->findArticle($id);

        return inertia('Projects/Articles/Edit', [
            'article' => $article,
            'contentItems' => $article->contentItems()
                ->with(['image', 'images', 'document', 'documents'])->get()->all(),
            'project' => $this->findProject($projectType, $projectId),
            'type' => $projectType,
        ]);
    }

    public function update(
        ProjectType $projectType,
        int $projectId,
        SaveArticleRequest $request,
        int $id
    ): RedirectResponse {
        $this->findArticle($id)
            ->update($request->validated());

        return redirect()->route('projects.articles.edit', [
            'projectType' => $projectType->id,
            'projectId' => $projectId,
            'id' => $id,
        ]);
    }

    public function updateStatus(
        ProjectType $projectType,
        int $projectId,
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

    public function destroy(ProjectType $projectType, int $projectId, int $id): RedirectResponse
    {
        $this->findArticle($id)->delete();

        return redirect()->route('projects.articles', [
            'projectType' => $projectType->id,
            'projectId' => $projectId,
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

    private function findProject(ProjectType $projectType, int $id): Project
    {
        return Project::withoutGlobalScope('published')
            ->with('mainImage')
            ->byType($projectType)
            ->findOrFail($id);
    }

    private function findArticle(int $id): Article
    {
        /** @var Article $article */
        $article = Article::withoutGlobalScope('published')->findOrFail($id);

        return $article;
    }
}
