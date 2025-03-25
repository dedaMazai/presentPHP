<?php

namespace App\Http\Admin\Controllers\SupportTopics;

use App\Components\QueryBuilder\Filters\FiltersCreatedBetween;
use App\Http\Admin\Controllers\Controller;
use App\Http\Admin\Requests\SaveSupportTopicsRequest;
use App\Models\SupportTopics;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use function inertia;
use function redirect;

/**
 * Class SupportTopicsController
 *
 * @package App\Http\Admin\Controllers\SupportTopics
 */
class SupportTopicsController extends Controller
{
    public function index(Request $request): Response
    {
        return inertia('SupportTopics/List', [
            'support-topics' => QueryBuilder::for(SupportTopics::class)
                ->withoutGlobalScope('published')
                ->allowedFilters([
                    AllowedFilter::partial('name'),
                    AllowedFilter::exact('is_published'),
                    AllowedFilter::custom('creation_period', new FiltersCreatedBetween()),
                    AllowedFilter::custom('update_period', new FiltersCreatedBetween()),
                ])
                ->allowedSorts([
                    'name', 'created_at', 'updated_at',
                ])
                ->paginate(),
            'defaultSorter' => $request->input('sort'),
            'defaultFilters' => $request->input('filter'),
        ]);
    }

    public function create(): Response
    {
        return inertia('SupportTopics/Create');
    }

    public function store(SaveSupportTopicsRequest $request): RedirectResponse
    {
        /** @var SupportTopics $supportTopics */
        $supportTopics = SupportTopics::create($request->validated());

        return redirect()->route('support-topics.edit', [
            'id' => $supportTopics->id,
        ]);
    }

    public function edit(int $id): Response
    {
        return inertia('SupportTopics/Edit', [
            'support-topics' => $this->findSupportTopics($id),
        ]);
    }

    public function update(SaveSupportTopicsRequest $request, int $id): RedirectResponse
    {
        $supportTopics = $this->findSupportTopics($id);
        $supportTopics->update($request->validated());

        return redirect()->route('support-topics.edit', [
            'id' => $supportTopics->id,
        ]);
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'is_published' => 'required|boolean',
        ]);

        $this->findSupportTopics($id)
            ->updatePublicationStatus($request->input('is_published'));

        return redirect()->back();
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->findSupportTopics($id)->delete();

        return redirect()->route('support-topics');
    }

    private function findSupportTopics(int $id): SupportTopics
    {
        return SupportTopics::withoutGlobalScope('published')->findOrFail($id);
    }
}
