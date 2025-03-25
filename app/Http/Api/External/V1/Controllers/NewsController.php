<?php

namespace App\Http\Api\External\V1\Controllers;

use App\Http\Resources\News\DetailNewsCollection;
use App\Http\Resources\News\DetailNewsResource;
use App\Http\Resources\News\NewsCategoryCollection;
use App\Http\Resources\News\NewsCollection;
use App\Models\News\News;
use App\Models\News\NewsCategory;
use App\Models\News\NewsType;
use App\Services\Account\AccountRepository;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Psr\SimpleCache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use function Amp\Iterator\toArray;

/**
 * Class NewsController
 *
 * @package App\Http\Api\External\V1\Controllers
 */
class NewsController extends Controller
{
    public function __construct(private AccountRepository $repository)
    {
    }

    /**
     * @throws ValidationException
     * @throws InvalidArgumentException
     * @throws AuthenticationException
     */
    public function index(Request $request): Response
    {
        $this->validate($request, [
            'type' => [
                'required',
                Rule::in(NewsType::toValues()),
            ],
            'category' => [
                Rule::in(NewsCategory::toValues()),
            ],
        ]);

        $type = NewsType::from($request->input('type'));
        $news = News::byType($type);

        if (isset($request->category)
            && isset($request->uk_project_id)
            && isset($request->buildings_id)
        ) {
            $news->byCategory(NewsCategory::tryFrom($request->input('category')));
            $uk_project_id = explode(",", $request->input('uk_project_id'));
            $buildings_id[] = '[]';
            $news = $news->where(function ($query) use ($buildings_id, $request, $uk_project_id) {
                foreach ($buildings_id as $building_id) {
                    $query->orWhereJsonContains('buildings_id', (int)$building_id);
                }
                $query->orWhere('buildings_id', '[]')->whereIn('uk_project_id', $uk_project_id);
                $query->orWhere('uk_project_id', null);
            });
        } elseif (isset($request->uk_project_id)
            && isset($request->buildings_id)
        ) {
            $buildings_id = explode(",", $request->input('buildings_id'));
            $uk_project_id = explode(",", $request->input('uk_project_id'));
            $buildings_id[] = '[]';
            $news = $news->where(function ($query) use ($buildings_id, $request, $uk_project_id) {
                foreach ($buildings_id as $building_id) {
                    $query->orWhereJsonContains('buildings_id', (int)$building_id);
                }
                $query->orWhere('buildings_id', '[]')->whereIn('uk_project_id', $uk_project_id);
                $query->orWhere('uk_project_id', null);
            });
        } elseif (isset($request->category)
            && isset($request->buildings_id)
        ) {
            $news->byCategory(NewsCategory::tryFrom($request->input('category')));
            $buildings_id = explode(",", $request->input('buildings_id'));
            $news = $news->where(function ($query) use ($buildings_id, $request) {
                foreach ($buildings_id as $building_id) {
                    $query->orWhereJsonContains('buildings_id', (int)$building_id);
                }
                $query->orWhere('uk_project_id', null);
            });
        } elseif (isset($request->uk_project_id)
            && isset($request->category)
        ) {
            $news->byCategory(NewsCategory::tryFrom($request->input('category')));
            $uk_project_id = explode(",", $request->input('uk_project_id'));
            $news = $news->where(function ($query) use ($uk_project_id) {
                $query->orWhereIn('uk_project_id', $uk_project_id);
                $query->orWhere('uk_project_id', null);
            });
        } elseif (isset($request->category)) {
            $news->byCategory(NewsCategory::tryFrom($request->input('category')));
        } elseif (isset($request->uk_project_id)) {
            $uk_project_id = explode(",", $request->input('uk_project_id'));
            $news = $news->where(function ($query) use ($uk_project_id) {
                $query->orWhereIn('uk_project_id', $uk_project_id);
                $query->orWhere('uk_project_id', null);
            });
        } elseif (isset($request->buildings_id)) {
            $buildings_id = explode(",", $request->input('buildings_id'));
            $news = $news->where(function ($query) use ($buildings_id, $request) {
                foreach ($buildings_id as $building_id) {
                    $query->orWhereJsonContains('buildings_id', (int)$building_id);
                }
                $query->orWhere('uk_project_id', null);
            });
        }

        return response()->json(new NewsCollection($news->latest()->paginate()));
    }

    public function show(int $id): Response
    {
        $news = $this->findNews($id);
        $news->increment('count');
        return response()->json(new DetailNewsResource($news));
    }

    public function category(): Response
    {
        $categories = [];

        foreach (NewsCategory::toArray() as $key => $value) {
            $categories[] = [
                "category" => $key,
                "name" => $value
            ];
        }

        return response()->json($categories);
    }

    private function findNews(int $id): News
    {
        /* @var News $news */
        $news = News::find($id);
        if ($news === null) {
            throw new NotFoundHttpException('News not found.');
        }

        return $news;
    }
}
