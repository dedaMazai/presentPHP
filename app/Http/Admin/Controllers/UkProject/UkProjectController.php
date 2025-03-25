<?php

namespace App\Http\Admin\Controllers\UkProject;

use App\Components\QueryBuilder\Filters\FiltersCreatedBetween;
use App\Http\Admin\Controllers\Controller;
use App\Http\Admin\Requests\SaveUkProjectRequest;
use App\Models\UkProject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use function inertia;
use function redirect;

/**
 * Class UkProjectController
 *
 * @package App\Http\Admin\Controllers\UkProject
 */
class UkProjectController extends Controller
{
    public function index(Request $request): Response
    {
        return inertia('UkProjects/List', [
            'uk-projects' => QueryBuilder::for(UkProject::class)
                ->withoutGlobalScope('published')
                ->allowedFilters([
                    AllowedFilter::exact('is_published'),
                    'name',
                    AllowedFilter::custom('creation_period', new FiltersCreatedBetween()),
                ])
                ->allowedSorts(['is_published', 'created_at', 'updated_at'])
                ->with('image')
                ->paginate(),
            'defaultSorter' => $request->input('sort'),
            'defaultFilters' => $request->input('filter'),
        ]);
    }

    public function create(): Response
    {
        return inertia('UkProjects/Create');
    }

    public function store(SaveUkProjectRequest $request): RedirectResponse
    {
        /** @var UkProject $ukProject */
        $ukProject = UkProject::create($request->validated());

        return redirect()->route('uk-projects.edit', [
            'id' => $ukProject->id,
        ]);
    }

    public function edit(int $id): Response
    {
        return inertia('UkProjects/Edit', [
            'uk-project' => $this->findUkProject($id),
        ]);
    }

    public function update(SaveUkProjectRequest $request, int $id): RedirectResponse
    {
        $ukProject = $this->findUkProject($id);
        $ukProject->update($request->validated());

        return redirect()->route('uk-projects.edit', [
            'id' => $ukProject->id,
        ]);
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'is_published' => 'required|boolean',
        ]);

        $this->findUkProject($id)
            ->updatePublicationStatus($request->input('is_published'));

        return redirect()->back();
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->findUkProject($id)->delete();

        return redirect()->route('uk-projects');
    }

    private function findUkProject(int $id): UkProject
    {
        return UkProject::withoutGlobalScope('published')
            ->with(['image', 'marketImage'])
            ->findOrFail($id);
    }
}
