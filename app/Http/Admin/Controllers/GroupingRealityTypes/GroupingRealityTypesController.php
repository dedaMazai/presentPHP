<?php

namespace App\Http\Admin\Controllers\GroupingRealityTypes;

use App\Components\QueryBuilder\Filters\FiltersCreatedBetween;
use App\Http\Admin\Controllers\Controller;
use App\Http\Admin\Requests\SaveGroupingRealityTypesRequest;
use App\Models\GroupingRealityTypes;
use App\Models\RealityTypes;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use function inertia;
use function redirect;

/**
 * Class GroupingRealityTypesController
 *
 * @package App\Http\Admin\Controllers\GroupingRealityTypes
 */
class GroupingRealityTypesController extends Controller
{
    public function index(Request $request): Response
    {
        return inertia('GroupingRealityTypes/List', [
            'groupingRealityTypes' => QueryBuilder::for(GroupingRealityTypes::class)
                ->allowedFilters([
                    AllowedFilter::partial('group_reality_name'),
                    AllowedFilter::exact('group_reality_ids'),
                    AllowedFilter::custom('creation_period', new FiltersCreatedBetween()),
                ])
                ->allowedSorts(['created_at', 'updated_at'])
                ->paginate(),
            'defaultSorter' => $request->input('sort'),
            'defaultFilters' => $request->input('filter'),
        ]);
    }

    public function create(): Response
    {
        return inertia('GroupingRealityTypes/Create', [
            'realityTypes' => RealityTypes::all(),
        ]);
    }

    public function store(SaveGroupingRealityTypesRequest $request): RedirectResponse
    {
        /** @var GroupingRealityTypes $realityTypes */
        $groupingRealityTypes = GroupingRealityTypes::create($request->validated());

        return redirect()->route('grouping-reality-types.edit', [
            'id' => $groupingRealityTypes->id,
        ]);
    }

    public function edit(int $id): Response
    {
        return inertia('GroupingRealityTypes/Edit', [
            'groupingRealityTypes' => $this->findGroupingRealityTypes($id),
            'realityTypes' => RealityTypes::all(),
        ]);
    }

    public function update(SaveGroupingRealityTypesRequest $request, int $id): RedirectResponse
    {
        $groupingRealityTypes = $this->findGroupingRealityTypes($id);
        $groupingRealityTypes->update($request->validated());

        return redirect()->route('grouping-reality-types.edit', [
            'id' => $groupingRealityTypes->id,
        ]);
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->findGroupingRealityTypes($id)->delete();

        return redirect()->route('grouping-reality-types');
    }

    private function findGroupingRealityTypes(int $id): GroupingRealityTypes
    {
        return GroupingRealityTypes::findOrFail($id);
    }
}
