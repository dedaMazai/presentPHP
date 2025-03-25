<?php

namespace App\Http\Admin\Controllers\EscrowBanks;

use App\Http\Admin\Controllers\Controller;
use App\Http\Admin\Requests\SaveEscrowBanksRequest;
use App\Models\EscrowBanks;
use App\Models\Banks;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Spatie\QueryBuilder\QueryBuilder;
use function inertia;
use function redirect;

/**
 * Class EscrowBanksController
 *
 * @package App\Http\Admin\Controllers\EscrowBanks
 */
class EscrowBanksController extends Controller
{
    public function index(Request $request): Response
    {
        return inertia('EscrowBanks/List', [
            'banks' => Banks::all(),
            'escrow-banks' => QueryBuilder::for(EscrowBanks::class)
                ->allowedFilters([
                    'escrow_bank_id',
                ])
                ->with('bank')
                ->allowedSorts(['escrow_bank_id'])
                ->paginate(),
            'defaultSorter' => $request->input('sort'),
            'defaultFilters' => $request->input('filter'),
        ]);
    }

    public function create(): Response
    {
        return inertia('EscrowBanks/Create', [
            'banks' => Banks::all(),
        ]);
    }

    public function store(SaveEscrowBanksRequest $request): RedirectResponse
    {
        /** @var EscrowBanks $escrowBanks */
        $escrowBanks = EscrowBanks::create($request->validated());

        return redirect()->route('escrow-banks.edit', [
            'id' => $escrowBanks->id,
        ]);
    }

    public function edit(int $id): Response
    {
        return inertia('EscrowBanks/Edit', [
            'banks' => Banks::all(),
            'escrow-banks' => $this->findEscrowBanks($id),
        ]);
    }

    public function update(SaveEscrowBanksRequest $request, int $id): RedirectResponse
    {
        $escrowBanks = $this->findEscrowBanks($id);
        $escrowBanks->update($request->validated());

        return redirect()->route('escrow-banks.edit', [
            'id' => $escrowBanks->id,
        ]);
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->findEscrowBanks($id)->delete();

        return redirect()->route('escrow-banks');
    }

    private function findEscrowBanks(int $id)
    {
        return EscrowBanks::with(['bank'])
            ->findOrFail($id);
    }
}
