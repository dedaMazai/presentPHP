<?php

namespace App\Http\Admin\Controllers;

use App\Components\QueryBuilder\Filters\FiltersCreatedBetween;
use App\Components\QueryBuilder\Filters\FiltersEndDate;
use App\Components\QueryBuilder\Filters\FiltersStartDate;
use App\Http\Admin\Requests\SaveAdRequest;
use App\Models\Ad\Ad;
use App\Models\Ad\AdPlace;
use App\Models\News\News;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class AdController
 *
 * @package App\Http\Admin\Controllers
 */
class AdController extends Controller
{
    public function index(Request $request): Response
    {
        return inertia('Ads/List', [
            'ads' => QueryBuilder::for(Ad::class)
                ->withoutGlobalScope('published')
                ->allowedFilters([
                    AllowedFilter::exact('is_published'),
                    AllowedFilter::exact('place'),
                    'title',
                    AllowedFilter::custom('creation_period', new FiltersCreatedBetween()),
                    AllowedFilter::custom('start_date', new FiltersStartDate()),
                    AllowedFilter::custom('end_date', new FiltersEndDate()),
                ])
                ->allowedSorts(['is_published', 'created_at', 'updated_at'])
                ->with(['image', 'news'])
                ->paginate(),
            'defaultSorter' => $request->input('sort'),
            'defaultFilters' => $request->input('filter'),
            'places' => AdPlace::toArray(),
        ]);
    }

    public function create(): Response
    {
        return inertia('Ads/Create', [
            'places' => AdPlace::toArray(),
            'news' => News::all(),
        ]);
    }

    public function store(SaveAdRequest $request): RedirectResponse
    {
        if ($request->get('end_date') == "") {
            $request->merge(['end_date' => null]);
        }
        if ($request->get('start_date') == "") {
            $request->merge(['start_date' => null]);
        }

        if ($request->get('mode') == 'news') {
            $ad = Ad::create(
                $request->only(
                    'is_published',
                    'place',
                    'title',
                    'subtitle',
                    'image_id',
                    'news_id',
                    'start_date',
                    'end_date',
                )
            );
        } elseif ($request->get('mode') == 'url') {
            $ad = Ad::create(
                $request->only(
                    'is_published',
                    'place',
                    'title',
                    'subtitle',
                    'image_id',
                    'url',
                    'start_date',
                    'end_date',
                )
            );
        }

        return redirect()->route('ads.edit', [
            'id' => $ad->id,
        ]);
    }

    public function edit(int $id): Response
    {
        return inertia('Ads/Edit', [
            'ad' => $this->findAd($id),
            'places' => AdPlace::toArray(),
            'news' => News::all(),
        ]);
    }

    private function findAd(int $id): Ad
    {
        return Ad::withoutGlobalScope('published')
            ->with(['image', 'news'])
            ->findOrFail($id);
    }

    public function update(SaveAdRequest $request, int $id): RedirectResponse
    {
        $ad = $this->findAd($id);

        if ($request->get('end_date') == "") {
            $request->merge(['end_date' => null]);
        }
        if ($request->get('start_date') == "") {
            $request->merge(['start_date' => null]);
        }

        if ($request->get('mode') == 'news') {
            $ad->update(
                $request->only(
                    'is_published',
                    'place',
                    'title',
                    'subtitle',
                    'image_id',
                    'news_id',
                    'start_date',
                    'end_date',
                )
            );
            $ad->update(['url' => null]);
        } elseif ($request->get('mode') == 'url') {
            $ad->update(
                $request->only(
                    'is_published',
                    'place',
                    'title',
                    'subtitle',
                    'image_id',
                    'url',
                    'start_date',
                    'end_date',
                )
            );
            $ad->update(['news_id' => null]);
        } else {
            $ad->update(
                $request->only(
                    'is_published',
                    'place',
                    'title',
                    'subtitle',
                    'image_id',
                    'start_date',
                    'end_date',
                )
            );
            $ad->update(['news_id' => null, 'url' => null]);
        }

        return redirect()->route('ads.edit', [
            'id' => $ad->id,
        ]);
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'is_published' => 'required|boolean',
        ]);

        $this->findAd($id)->updatePublicationStatus($request->input('is_published'));

        return redirect()->back();
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->findAd($id)->delete();

        return redirect()->back();
    }
}
