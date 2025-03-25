<?php

namespace App\Http\Admin\Controllers\UkProject;

use App\Components\QueryBuilder\Filters\FiltersCreatedBetween;
use App\Http\Admin\Controllers\Controller;
use App\Http\Admin\Requests\SaveUkBuildingRequest;
use App\Models\Building\Building;
use App\Models\UkProject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class UkBuildingController
 *
 * @package App\Http\Admin\Controllers\UkBuilding
 */
class UkBuildingController extends Controller
{
    public function index(int $ukProjectId, Request $request): Response
    {
        $ukProject = $this->findUkProject($ukProjectId);

        return inertia('UkProjects/Buildings/List', [
            'buildings' => QueryBuilder::for(Building::class)
                ->byUkProject($ukProject)
                ->allowedFilters([
                    'build_name',
                    'build_zid',
                    AllowedFilter::custom('creation_period', new FiltersCreatedBetween()),
                ])
                ->allowedSorts(['created_at', 'updated_at'])
                ->paginate(),
            'defaultFilters' => $request->input('filter'),
            'defaultSorter' => $request->input('sort'),
            'ukProject' => $ukProject,
        ]);
    }

    public function create(int $ukProjectId): Response
    {
        return inertia('UkProjects/Buildings/Create', [
            'ukProject' => $this->findUkProject($ukProjectId),
        ]);
    }

    public function store(int $ukProjectId, SaveUkBuildingRequest $request): RedirectResponse
    {
        /** @var Building $building */
        $building = $this->findUkProject($ukProjectId)
            ->buildings()
            ->create($request->validated());

        return redirect()->route('uk-projects.buildings.edit', [
            'ukProjectId' => $ukProjectId,
            'id' => $building->id,
        ]);
    }

    public function edit(int $ukProjectId, int $id): Response
    {
        $building = $this->findBuilding($id);

        return inertia('UkProjects/Buildings/Edit', [
            'building' => $building,
            'ukProject' => $this->findUkProject($ukProjectId),
        ]);
    }

    public function update(
        int $ukProjectId,
        SaveUkBuildingRequest $request,
        int $id
    ): RedirectResponse {
        $this->findBuilding($id)
            ->update($request->validated());

        return redirect()->route('uk-projects.buildings.edit', [
            'ukProjectId' => $ukProjectId,
            'id' => $id,
        ]);
    }

    public function destroy(int $ukProjectId, int $id): RedirectResponse
    {
        $this->findBuilding($id)->delete();

        return redirect()->route('uk-projects.buildings', [
            'ukProjectId' => $ukProjectId,
        ]);
    }

    public function sort(Request $request): RedirectResponse
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer',
        ]);

        Building::setNewOrder($request->input('order'));

        return redirect()->back();
    }

    private function findUkProject(int $id): UkProject
    {
        return UkProject::with('image')->findOrFail($id);
    }

    private function findBuilding(int $id): Building
    {
        return Building::findOrFail($id);
    }
}
