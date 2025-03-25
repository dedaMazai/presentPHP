<?php

namespace App\Http\Admin\Controllers;

use App\Components\QueryBuilder\Filters\FiltersCreatedBetween;
use App\Http\Admin\Requests\SaveProjectRequest;
use App\Models\City;
use App\Models\Mortgage\MortgageType;
use App\Models\Project\Project;
use App\Models\Project\ProjectPropertyType;
use App\Models\Project\ProjectType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class ProjectController
 *
 * @package App\Http\Admin\Controllers
 */
class ProjectController extends Controller
{
    public function index(ProjectType $projectType, Request $request): Response
    {
        return inertia('Projects/List', [
            'projects' => QueryBuilder::for(Project::class)
                ->withoutGlobalScope('published')
                ->allowedFilters([
                    AllowedFilter::exact('is_published'),
                    'name',
                    AllowedFilter::custom('creation_period', new FiltersCreatedBetween()),
                ])
                ->allowedSorts(['is_published', 'created_at', 'updated_at'])
                ->with('mainImage')
                ->byType($projectType)
                ->paginate(),
            'defaultSorter' => $request->input('sort'),
            'defaultFilters' => $request->input('filter'),
            'type' => $projectType,
            'types' => ProjectType::all(),
        ]);
    }

    public function create(ProjectType $projectType): Response
    {
        return inertia('Projects/Create', [
            'type' => $projectType,
            'propertyTypes' => ProjectPropertyType::toArray(),
            'cities' => City::all(),
            'mortgageTypes' => MortgageType::collectCases(),
        ]);
    }

    public function store(ProjectType $projectType, SaveProjectRequest $request): RedirectResponse
    {
        $data = $request->only(
            'is_published',
            'name',
            'showcase_image_id',
            'main_image_id',
            'map_image_id',
            'metro',
            'metro_color',
            'crm_ids',
            'mortgage_calculator_id',
            'lat',
            'long',
            'office_phone',
            'office_address',
            'office_lat',
            'office_long',
            'office_work_hours',
            'property_type_params',
            'color',
            'description',
            'city_id',
            'mortgage_types',
            'payroll_bank_programs',
            'mortgage_min_property_price',
            'mortgage_max_property_price',
            'booking_property',
            'is_premium',
            'url_memo'
        );
        $data['type_id'] = $projectType->id;

        /** @var Project $project */
        $project = Project::create($data);
        $project->images()->sync($request->input('image_ids'));

        return redirect()->route('projects.edit', [
            'projectType' => $projectType->id,
            'id' => $project->id,
            'mortgageTypes' => MortgageType::collectCases(),
        ]);
    }

    public function edit(ProjectType $projectType, int $id): Response
    {
        return inertia('Projects/Edit', [
            'project' => $this->findProject($projectType, $id),
            'type' => $projectType,
            'propertyTypes' => ProjectPropertyType::toArray(),
            'cities' => City::all(),
            'mortgageTypes' => MortgageType::collectCases(),
        ]);
    }

    public function update(ProjectType $projectType, SaveProjectRequest $request, int $id): RedirectResponse
    {
        $project = $this->findProject($projectType, $id);
        $project->update(
            $request->only(
                'is_published',
                'name',
                'showcase_image_id',
                'main_image_id',
                'map_image_id',
                'metro',
                'metro_color',
                'crm_ids',
                'mortgage_calculator_id',
                'lat',
                'long',
                'office_phone',
                'office_address',
                'office_lat',
                'office_long',
                'office_work_hours',
                'property_type_params',
                'color',
                'description',
                'city_id',
                'mortgage_types',
                'payroll_bank_programs',
                'mortgage_min_property_price',
                'mortgage_max_property_price',
                'booking_property',
                'is_premium',
                'url_memo'
            )
        );
        $imageIds = $request->input('image_ids');
        $project->images()->detach($imageIds);
        $project->images()->sync($imageIds);

        return redirect()->route('projects.edit', [
            'projectType' => $projectType->id,
            'id' => $project->id,
            'cities' => City::all(),
            'mortgageTypes' => MortgageType::collectCases(),
        ]);
    }

    public function updateStatus(ProjectType $projectType, Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'is_published' => 'required|boolean',
        ]);

        $this->findProject($projectType, $id)->updatePublicationStatus($request->input('is_published'));

        return redirect()->back();
    }

    public function destroy(ProjectType $projectType, int $id): RedirectResponse
    {
        $this->findProject($projectType, $id)->delete();

        return redirect()->route('projects', [
            'projectType' => $projectType->id,
        ]);
    }

    public function sort(Request $request): RedirectResponse
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer',
        ]);

        Project::setNewOrder($request->input('order'));

        return redirect()->back();
    }

    private function findProject(ProjectType $projectType, int $id): Project
    {
        return Project::withoutGlobalScope('published')
            ->with(['showcaseImage', 'mainImage', 'mapImage', 'images'])
            ->byType($projectType)
            ->findOrFail($id);
    }
}
