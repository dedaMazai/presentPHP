<?php

namespace App\Http\Api\External\V1\Controllers;

use App\Http\Resources\Article\ArticleCollection;
use App\Http\Resources\Article\DetailArticleResource;
use App\Models\Article\Article;
use App\Models\Project\Project;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ArticleController
 *
 * @package App\Http\Api\External\V1\Controllers
 */
class ArticleController extends Controller
{
    public function index(int $projectId): Response
    {
        return response()->json(new ArticleCollection($this->findProject($projectId)->articles));
    }

    public function show(int $projectId, int $id): Response
    {
        return response()->json(new DetailArticleResource($this->findArticle($projectId, $id)));
    }

    private function findArticle(int $projectId, int $id): Article
    {
        /* @var Article $article */
        $article = $this->findProject($projectId)->articles()->find($id);
        if ($article === null) {
            throw new NotFoundHttpException('Article not found.');
        }

        return $article;
    }

    private function findProject(int $id): Project
    {
        /* @var Project $project */
        $project = Project::find($id);
        if ($project === null) {
            throw new NotFoundHttpException('Project not found.');
        }

        return $project;
    }
}
