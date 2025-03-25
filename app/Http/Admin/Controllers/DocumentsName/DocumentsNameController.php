<?php

namespace App\Http\Admin\Controllers\DocumentsName;

use App\Http\Admin\Controllers\Controller;
use App\Http\Admin\Requests\SaveDocumentsNameRequest;
use App\Models\DocumentsName;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use function inertia;
use function redirect;

/**
 * Class DocumentsNameController
 *
 * @package App\Http\Admin\Controllers\DocumentsName
 */
class DocumentsNameController extends Controller
{
    public function index(Request $request): Response
    {
        return inertia('DocumentsName/List', [
            'documents-name' => QueryBuilder::for(DocumentsName::class)
                ->allowedFilters([
                    'code',
                    'name',
                    'description',
                    AllowedFilter::exact('object_type_code'),
                ])
                ->allowedSorts([
                    'code',
                    'name'
                ])
                ->paginate(),
            'defaultSorter' => $request->input('sort'),
            'defaultFilters' => $request->input('filter'),
        ]);
    }

    public function create(): Response
    {
        return inertia('DocumentsName/Create');
    }

    public function store(SaveDocumentsNameRequest $request): RedirectResponse
    {
        /** @var DocumentsName $documentsName */
        $documentsName = DocumentsName::create($request->validated());

        return redirect()->route('documents-name.edit', [
            'id' => $documentsName->id,
        ]);
    }

    public function edit(int $id): Response
    {
        return inertia('DocumentsName/Edit', [
            'documents-name' => $this->findDocumentsName($id),
        ]);
    }

    public function update(SaveDocumentsNameRequest $request, int $id): RedirectResponse
    {
        $documentsName = $this->findDocumentsName($id);
        $documentsName->update($request->validated());

        return redirect()->route('documents-name.edit', [
            'id' => $documentsName->id,
        ]);
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->findDocumentsName($id)->delete();

        return redirect()->route('documents-name');
    }

    private function findDocumentsName(int $id)
    {
        return DocumentsName::findOrFail($id);
    }
}
