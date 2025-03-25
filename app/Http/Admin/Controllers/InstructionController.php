<?php

namespace App\Http\Admin\Controllers;

use App\Components\QueryBuilder\Filters\FiltersCreatedBetween;
use App\Http\Admin\Requests\SaveInstructionRequest;
use App\Models\Instruction\Instruction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class InstructionController
 *
 * @package App\Http\Admin\Controllers
 */
class InstructionController extends Controller
{
    public function index(Request $request): Response
    {
        return inertia('Instructions/List', [
            'instructions' => QueryBuilder::for(Instruction::class)
                ->withoutGlobalScope('published')
                ->allowedFilters([
                    AllowedFilter::exact('is_published'),
                    'title',
                    AllowedFilter::custom('creation_period', new FiltersCreatedBetween()),
                ])
                ->allowedSorts(['is_published', 'created_at', 'updated_at'])
                ->with(['image'])
                ->paginate(),
            'defaultSorter' => $request->input('sort'),
            'defaultFilters' => $request->input('filter'),
        ]);
    }

    public function create(): Response
    {
        return inertia('Instructions/Create');
    }

    public function store(SaveInstructionRequest $request): RedirectResponse
    {
        /** @var Instruction $instruction */
        $instruction = Instruction::create(
            $request->only(
                'is_published',
                'title',
                'image_id',
                'text',
            )
        );

        return redirect()->route('instructions.edit', [
            'id' => $instruction->id,
        ]);
    }

    public function edit(int $id): Response
    {
        return inertia('Instructions/Edit', [
            'instruction' => $this->findInstruction($id),
        ]);
    }

    private function findInstruction(int $id): Instruction
    {
        return Instruction::withoutGlobalScope('published')
            ->with(['image'])
            ->findOrFail($id);
    }

    public function update(int $id, SaveInstructionRequest $request): RedirectResponse
    {
        $instruction = $this->findInstruction($id);
        $instruction->update(
            $request->only(
                'is_published',
                'title',
                'image_id',
                'text',
            )
        );

        return redirect()->route('instructions.edit', [
            'id' => $instruction->id,
        ]);
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'is_published' => 'required|boolean',
        ]);

        $this->findInstruction($id)->updatePublicationStatus($request->input('is_published'));

        return redirect()->back();
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->findInstruction($id)->delete();

        return redirect()->route('instructions');
    }

    public function sort(Request $request): RedirectResponse
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer',
        ]);

        Instruction::setNewOrder($request->input('order'));

        return redirect()->back();
    }
}
