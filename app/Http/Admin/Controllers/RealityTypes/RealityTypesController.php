<?php

namespace App\Http\Admin\Controllers\RealityTypes;

use App\Components\QueryBuilder\Filters\FiltersCreatedBetween;
use App\Http\Admin\Controllers\Controller;
use App\Http\Admin\Requests\SaveRealityTypesRequest;
use App\Models\RealityTypes;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use function inertia;
use function redirect;

/**
 * Class RealityTypesController
 *
 * @package App\Http\Admin\Controllers\RealityTypes
 */
class RealityTypesController extends Controller
{
    public function index(Request $request): Response
    {
        return inertia('RealityTypes/List', [
            'reality-types' => QueryBuilder::for(RealityTypes::class)
                ->allowedFilters([
                    AllowedFilter::partial('reality_name'),
                    AllowedFilter::exact('reality_id'),
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
        return inertia('RealityTypes/Create');
    }

    public function store(SaveRealityTypesRequest $request): RedirectResponse
    {
        /** @var RealityTypes $realityTypes */
        $realityTypes = RealityTypes::create($request->validated());

        return redirect()->route('reality-types.edit', [
            'id' => $realityTypes->id,
        ]);
    }

    public function edit(int $id): Response
    {
        return inertia('RealityTypes/Edit', [
            'reality-types' => $this->findRealityTypes($id),
        ]);
    }

    public function update(SaveRealityTypesRequest $request, int $id): RedirectResponse
    {
        $realityTypes = $this->findRealityTypes($id);
        $realityTypes->update($request->validated());

        return redirect()->route('reality-types.edit', [
            'id' => $realityTypes->id,
        ]);
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->findRealityTypes($id)->delete();

        return redirect()->route('reality-types');
    }

    private function findRealityTypes(int $id): RealityTypes
    {
        return RealityTypes::findOrFail($id);
    }
}
