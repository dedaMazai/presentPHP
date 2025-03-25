<?php

namespace App\Http\Admin\Controllers\Banks;

use App\Http\Admin\Controllers\Controller;
use App\Http\Admin\Requests\SaveBanksRequest;
use App\Models\Banks;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Spatie\QueryBuilder\QueryBuilder;
use function inertia;
use function redirect;

/**
 * Class BanksController
 *
 * @package App\Http\Admin\Controllers\Banks
 */
class BanksController extends Controller
{
    public function index(Request $request): Response
    {
        return inertia('Banks/List', [
            'banks' => QueryBuilder::for(Banks::class)
                ->allowedFilters([
                    'name',
                    'bank_id',
                ])
                ->allowedSorts(['name', 'bank_id'])
                ->with('image')
                ->paginate(),
            'defaultSorter' => $request->input('sort'),
            'defaultFilters' => $request->input('filter'),
        ]);
    }

    public function create(): Response
    {
        return inertia('Banks/Create');
    }

    public function store(SaveBanksRequest $request): RedirectResponse
    {
        /** @var Banks $banks */
        $banks = Banks::create($request->validated());

        return redirect()->route('banks.edit', [
            'id' => $banks->id,
        ]);
    }

    public function edit(int $id): Response
    {
        return inertia('Banks/Edit', [
            'banks' => $this->findBanks($id),
        ]);
    }

    public function update(SaveBanksRequest $request, int $id): RedirectResponse
    {
        $banks = $this->findBanks($id);
        $banks->update($request->validated());

        return redirect()->route('banks.edit', [
            'id' => $banks->id,
        ]);
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->findBanks($id)->delete();

        return redirect()->route('banks');
    }

    private function findBanks(int $id)
    {
        return Banks::with(['image'])
            ->findOrFail($id);
    }
}
