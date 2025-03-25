<?php

namespace App\Http\Admin\Controllers\SignDocuments;

use App\Http\Admin\Controllers\Controller;
use App\Http\Admin\Requests\SaveSignDocumentsRequest;
use App\Models\SignDocuments;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Spatie\QueryBuilder\QueryBuilder;
use function inertia;
use function redirect;

/**
 * Class SignDocumentsController
 *
 * @package App\Http\Admin\Controllers\SignDocuments
 */
class SignDocumentsController extends Controller
{
    public function index(Request $request): Response
    {
        return inertia('SignDocuments/List', [
            'sign-documents' => QueryBuilder::for(SignDocuments::class)
                ->allowedFilters([
                    'name',
                    'code',
                ])
                ->allowedSorts([
                    'name',
                    'code',
                ])
                ->paginate(),
            'defaultSorter' => $request->input('sort'),
            'defaultFilters' => $request->input('filter'),
        ]);
    }

    public function create(): Response
    {
        return inertia('SignDocuments/Create');
    }

    public function store(SaveSignDocumentsRequest $request): RedirectResponse
    {
        /** @var SignDocuments $signDocuments */
        $signDocuments = SignDocuments::create($request->validated());

        return redirect()->route('sign-documents.edit', [
            'id' => $signDocuments->id,
        ]);
    }

    public function edit(int $id): Response
    {
        return inertia('SignDocuments/Edit', [
            'sign-documents' => $this->findSignDocuments($id),
        ]);
    }

    public function update(SaveSignDocumentsRequest $request, int $id): RedirectResponse
    {
        $signDocuments= $this->findSignDocuments($id);
        $signDocuments->update($request->validated());

        return redirect()->route('sign-documents.edit', [
            'id' => $signDocuments->id,
        ]);
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->findSignDocuments($id)->delete();

        return redirect()->route('sign-documents');
    }

    private function findSignDocuments(int $id)
    {
        return SignDocuments::findOrFail($id);
    }
}
