<?php

namespace App\Http\Admin\Controllers;

use App\Components\QueryBuilder\Filters\FiltersCreatedBetween;
use App\Http\Admin\Requests\SaveBankInfoRequest;
use App\Models\Sales\Bank\BankInfo;
use App\Models\Sales\Bank\BankType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class BankInfoController
 *
 * @package App\Http\Admin\Controllers
 */
class BankInfoController extends Controller
{
    public function index(Request $request): Response
    {
        return inertia('BankInfo/List', [
            'bank-info' => QueryBuilder::for(BankInfo::class)
                ->withoutGlobalScope('published')
                ->allowedFilters([
                    AllowedFilter::exact('is_published'),
                    AllowedFilter::exact('type'),
                    'title',
                    AllowedFilter::custom('creation_period', new FiltersCreatedBetween()),
                ])
                ->allowedSorts(['is_published', 'created_at', 'updated_at'])
                ->with(['logoImage'])
                ->paginate(),
            'defaultSorter' => $request->input('sort'),
            'defaultFilters' => $request->input('filter'),
            'types' => BankType::toArray(),
        ]);
    }

    public function create(): Response
    {
        return inertia('BankInfo/Create', [
            'types' => BankType::toArray(),
        ]);
    }

    public function store(SaveBankInfoRequest $request): RedirectResponse
    {
        /** @var BankInfo $bankInfo */
        $bankInfo = BankInfo::create($request->validated());

        return redirect()->route('bank-info.edit', [
            'id' => $bankInfo->id,
        ]);
    }

    public function edit(int $id): Response
    {
        return inertia('BankInfo/Edit', [
            'bank-info' => $this->findBankInfo($id),
            'types' => BankType::toArray(),
        ]);
    }

    public function update(int $id, SaveBankInfoRequest $request): RedirectResponse
    {
        $bankInfo = $this->findBankInfo($id);
        $bankInfo->update($request->validated());

        return redirect()->route('bank-info.edit', [
            'id' => $bankInfo->id,
        ]);
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'is_published' => 'required|boolean',
        ]);

        $this->findBankInfo($id)
            ->updatePublicationStatus($request->input('is_published'));

        return redirect()->back();
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->findBankInfo($id)->delete();

        return redirect()->route('banks');
    }

    public function sort(Request $request): RedirectResponse
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer',
        ]);

        BankInfo::setNewOrder($request->input('order'));

        return redirect()->back();
    }

    private function findBankInfo(int $id): BankInfo
    {
        return BankInfo::withoutGlobalScope('published')
            ->with(['logoImage'])
            ->findOrFail($id);
    }
}
