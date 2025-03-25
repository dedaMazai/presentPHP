<?php

namespace App\Http\Admin\Controllers;

use App\Components\QueryBuilder\Filters\FiltersCreatedBetween;
use App\Http\Admin\Requests\SaveMortgageProgramRequest;
use App\Models\MortgageProgram;
use App\Models\Project\Project;
use App\Models\Project\ProjectType;
use App\Models\Sales\Bank\BankInfo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class MortgageProgramController
 *
 * @package App\Http\Admin\Controllers
 */
class MortgageProgramController extends Controller
{
    public function index(ProjectType $projectType, int $projectId, Request $request): Response
    {
        $project = $this->findProject($projectType, $projectId);

        return inertia('Projects/MortgagePrograms/List', [
            'mortgagePrograms' => QueryBuilder::for(MortgageProgram::class)
                ->withoutGlobalScope('published')
                ->byProject($project)
                ->allowedFilters([
                    AllowedFilter::custom('creation_period', new FiltersCreatedBetween()),
                ])
                ->allowedSorts(['created_at', 'updated_at'])
                ->with(['bankInfo'])
                ->paginate(),
            'defaultSorter' => $request->input('sort'),
            'defaultFilters' => $request->input('filter'),
            'project' => $project,
            'type' => $projectType,
        ]);
    }

    public function create(ProjectType $projectType, int $projectId): Response
    {
        return inertia('Projects/MortgagePrograms/Create', [
            'project' => $this->findProject($projectType, $projectId),
            'type' => $projectType,
            'banks' => BankInfo::all(),
        ]);
    }

    public function store(
        ProjectType $projectType,
        int $projectId,
        SaveMortgageProgramRequest $request
    ): RedirectResponse {
        $data = $request->validated();
        $data['project_id'] = $projectId;

        /** @var MortgageProgram $mortgageProgram */
        $mortgageProgram = $this->findProject($projectType, $projectId)
            ->mortgagePrograms()
            ->create($data);

        return redirect()->route('projects.mortgage-programs.edit', [
            'projectType' => $projectType->id,
            'projectId' => $projectId,
            'id' => $mortgageProgram->id,
        ]);
    }

    public function edit(ProjectType $projectType, int $projectId, int $id): Response
    {
        $mortgageProgram = $this->findMortgageProgram($id);

        return inertia('Projects/MortgagePrograms/Edit', [
            'mortgageProgram' => $mortgageProgram,
            'project' => $this->findProject($projectType, $projectId),
            'type' => $projectType,
            'banks' => BankInfo::all(),
        ]);
    }

    public function update(
        ProjectType $projectType,
        int $projectId,
        int $id,
        SaveMortgageProgramRequest $request,
    ): RedirectResponse {
        $this->findMortgageProgram($id)
            ->update($request->validated());

        return redirect()->route('projects.mortgage-programs.edit', [
            'projectType' => $projectType->id,
            'projectId' => $projectId,
            'id' => $id,
        ]);
    }

    public function destroy(ProjectType $projectType, int $projectId, int $id): RedirectResponse
    {
        $this->findMortgageProgram($id)->delete();

        return redirect()->route('projects.mortgage-programs', [
            'projectType' => $projectType->id,
            'projectId' => $projectId,
        ]);
    }

    public function sort(Request $request): RedirectResponse
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer',
        ]);

        MortgageProgram::setNewOrder($request->input('order'));

        return redirect()->back();
    }

    private function findProject(ProjectType $projectType, int $id): Project
    {
        return Project::withoutGlobalScope('published')
            ->with('mainImage')
            ->byType($projectType)
            ->findOrFail($id);
    }

    private function findMortgageProgram(int $id): MortgageProgram
    {
        /** @var MortgageProgram $mortgageProgram */
        $mortgageProgram = MortgageProgram::withoutGlobalScope('published')->findOrFail($id);

        return $mortgageProgram;
    }
}
