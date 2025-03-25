<?php

namespace App\Http\Admin\Controllers\ClientRoleTypes;

use App\Components\QueryBuilder\Filters\FiltersCreatedBetween;
use App\Http\Admin\Controllers\Controller;
use App\Http\Admin\Requests\SaveClientRoleTypesRequest;
use App\Models\ClientRoleTypes;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use function inertia;
use function redirect;

/**
 * Class ClientRoleTypesController
 *
 * @package App\Http\Admin\Controllers\ClientRoleTypes
 */
class ClientRoleTypesController extends Controller
{
    public function index(Request $request): Response
    {
        return inertia('ClientRoleTypes/List', [
            'client-role-types' => QueryBuilder::for(ClientRoleTypes::class)
                ->allowedFilters([
                    'role_name',
                    'role_code',
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
        return inertia('ClientRoleTypes/Create');
    }

    public function store(SaveClientRoleTypesRequest $request): RedirectResponse
    {
        /** @var ClientRoleTypes $clientRoleTypes */
        $clientRoleTypes = ClientRoleTypes::create($request->validated());

        return redirect()->route('client-role-types.edit', [
            'id' => $clientRoleTypes->id,
        ]);
    }

    public function edit(int $id): Response
    {
        return inertia('ClientRoleTypes/Edit', [
            'client-role-types' => $this->findClientRoleTypes($id),
        ]);
    }

    public function update(SaveClientRoleTypesRequest $request, int $id): RedirectResponse
    {
        $clientRoleTypes = $this->findClientRoleTypes($id);
        $clientRoleTypes->update($request->validated());

        return redirect()->route('client-role-types.edit', [
            'id' => $clientRoleTypes->id,
        ]);
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->findClientRoleTyps($id)->delete();

        return redirect()->route('client-role-types');
    }

    private function findClientRoleTypes(int $id): ClientRoleTypes
    {
        return ClientRoleTypes::findOrFail($id);
    }
}
