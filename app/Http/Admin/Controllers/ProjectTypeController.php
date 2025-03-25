<?php

namespace App\Http\Admin\Controllers;

use App\Components\QueryBuilder\Filters\FiltersCreatedBetween;
use App\Models\Project\ProjectType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class ProjectTypeController
 *
 * @package App\Http\Admin\Controllers
 */
class ProjectTypeController extends Controller
{
    public function index(Request $request): Response
    {
        return inertia('Projects/Types/List', [
            'projectTypes' => QueryBuilder::for(ProjectType::class)
                ->allowedFilters([
                    'name',
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
        return inertia('Projects/Types/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        /** @var ProjectType $projectType */
        $projectType = ProjectType::create($request->only(
            'name',
        ));

        return redirect()->route('project-types.edit', [
            'id' => $projectType->id,
        ]);
    }

    public function edit(int $id): Response
    {
        return inertia('Projects/Types/Edit', [
            'projectType' => $this->findProjectType($id),
        ]);
    }

    private function findProjectType(int $id): ProjectType
    {
        return ProjectType::findOrFail($id);
    }

    public function update(int $id, Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $projectType = $this->findProjectType($id);
        $projectType->update(
            $request->only(
                'name',
            )
        );

        return redirect()->route('project-types.edit', [
            'id' => $projectType->id,
        ]);
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->findProjectType($id)->delete();

        return redirect()->back();
    }

    public function sort(Request $request): RedirectResponse
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer',
        ]);

        ProjectType::setNewOrder($request->input('order'));

        return redirect()->back();
    }
}
