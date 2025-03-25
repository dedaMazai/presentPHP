<?php

namespace App\Http\Admin\Controllers;

use App\Components\QueryBuilder\Filters\FiltersCreatedBetween;
use App\Components\QueryBuilder\Filters\FiltersEndDate;
use App\Components\QueryBuilder\Filters\FiltersStartDate;
use App\Http\Admin\Requests\SaveBannerRequest;
use App\Models\Banner\Banner;
use App\Models\Banner\BannerPlace;
use App\Models\News\News;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class BannerController
 *
 * @package App\Http\Admin\Controllers
 */
class BannerController extends Controller
{
    public function places(): Response
    {
        return inertia('Banners/Places', [
            'places' => BannerPlace::toArray(),
        ]);
    }

    public function index(BannerPlace $place, Request $request): Response
    {
        return inertia('Banners/List', [
            'banners' => QueryBuilder::for(Banner::class)
                ->withoutGlobalScope('published')
                ->allowedFilters([
                    AllowedFilter::exact('is_published'),
                    'title',
                    AllowedFilter::custom('creation_period', new FiltersCreatedBetween()),
                    AllowedFilter::custom('start_date', new FiltersStartDate()),
                    AllowedFilter::custom('end_date', new FiltersEndDate()),
                ])
                ->allowedSorts(['is_published', 'created_at', 'updated_at'])
                ->with(['image', 'news'])
                ->byPlace($place)
                ->paginate(),
            'defaultSorter' => $request->input('sort'),
            'defaultFilters' => $request->input('filter'),
            'place' => $place->value,
            'places' => BannerPlace::toArray(),
        ]);
    }

    public function create(BannerPlace $place): Response
    {
        return inertia('Banners/Create', [
            'place' => $place->value,
            'places' => BannerPlace::toArray(),
            'news' => News::all(),
        ]);
    }

    public function store(BannerPlace $place, SaveBannerRequest $request): RedirectResponse
    {
        if ($request->get('end_date') == "") {
            $request->merge(['end_date' => null]);
        }
        if ($request->get('start_date') == "") {
            $request->merge(['start_date' => null]);
        }

        if ($request->get('mode') == 'news') {
            $data = $request->only(
                'is_published',
                'image_id',
                'news_id',
                'category_crm_id',
                'start_date',
                'end_date',
            );
        } elseif ($request->get('mode') == 'url') {
            $data = $request->only(
                'is_published',
                'image_id',
                'category_crm_id',
                'url',
                'start_date',
                'end_date',
            );
        }

        $data['place'] = $place;

        /** @var Banner $banner */
        $banner = Banner::create($data);

        return redirect()->route('banners.edit', [
            'place' => $place->value,
            'id' => $banner->id,
        ]);
    }

    public function edit(BannerPlace $place, int $id): Response
    {
        return inertia('Banners/Edit', [
            'banner' => $this->findBanner($place, $id),
            'news' => News::all(),
            'place' => $place->value,
            'places' => BannerPlace::toArray(),
        ]);
    }

    private function findBanner(BannerPlace $place, int $id): Banner
    {
        return Banner::withoutGlobalScope('published')
            ->with(['image', 'news'])
            ->where(['place' => $place->value])
            ->findOrFail($id);
    }

    public function update(BannerPlace $place, int $id, SaveBannerRequest $request): RedirectResponse
    {
        $banner = $this->findBanner($place, $id);

        if ($request->get('end_date') == "") {
            $request->merge(['end_date' => null]);
        }
        if ($request->get('start_date') == "") {
            $request->merge(['start_date' => null]);
        }

        if ($request->get('mode') == 'news') {
            $banner->update(
                $request->only(
                    'is_published',
                    'image_id',
                    'news_id',
                    'category_crm_id',
                    'start_date',
                    'end_date',
                ),
            );
            $banner->update(['url' => null]);
        } elseif ($request->get('mode') == 'url') {
            $banner->update(
                $request->only(
                    'is_published',
                    'image_id',
                    'category_crm_id',
                    'url',
                    'start_date',
                    'end_date',
                )
            );
            $banner->update(['news_id' => null]);
        }


        return redirect()->route('banners.edit', [
            'place' => $place->value,
            'id' => $banner->id,
        ]);
    }

    public function updateStatus(BannerPlace $place, Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'is_published' => 'required|boolean',
        ]);

        $this->findBanner($place, $id)->updatePublicationStatus($request->input('is_published'));

        return redirect()->back();
    }

    public function destroy(BannerPlace $place, int $id): RedirectResponse
    {
        $this->findBanner($place, $id)->delete();

        return redirect()->back();
    }

    public function sort(Request $request): RedirectResponse
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer',
        ]);

        Banner::setNewOrder($request->input('order'));

        return redirect()->back();
    }
}
