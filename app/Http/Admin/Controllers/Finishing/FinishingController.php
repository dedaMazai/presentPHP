<?php

namespace App\Http\Admin\Controllers\Finishing;

use App\Http\Admin\Controllers\Controller;
use App\Http\Admin\Requests\SaveFinishingRequest;
use App\Models\Finishing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use function inertia;
use function redirect;

/**
 * Class FinishingController
 *
 * @package App\Http\Admin\Controllers\Finishing
 */
class FinishingController extends Controller
{
    public function index(Request $request): Response
    {
        return inertia('Finishings/List', [
            'finishings' => QueryBuilder::for(Finishing::class)
                ->withoutGlobalScope('published')
                ->allowedFilters([
                    'finishing_id',
                    'description',
                    AllowedFilter::exact('is_published'),
                ])
                ->allowedSorts(['is_published'])
                ->with(['images'])
                ->paginate(),
            'defaultSorter' => $request->input('sort'),
            'defaultFilters' => $request->input('filter'),
        ]);
    }

    public function create(): Response
    {
        return inertia('Finishings/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var Finishing $finnishings */
        $finishing = Finishing::create($request->all());
        $finishing->images()->sync($request->input('images_id'));

        return redirect()->route('finishings.edit', [
            'id' => $finishing->id,
        ]);
    }

    public function edit(int $id): Response
    {
        return inertia('Finishings/Edit', [
            'finishing' => $this->findFinishing($id),
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $finishing = $this->findFinishing($id);
        $finishing->update($request->all());

        $images_id = $request->input('images_id');
        $images_id = array_diff($images_id, array(''));
        foreach ($request->input('images') as $image) {
            $images_id[] = $image['id'];
        }
        $finishing->images()->detach($images_id);
        $finishing->images()->sync($images_id);

        return redirect()->route('finishings.edit', [
            'id' => $finishing->id,
        ]);
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'is_published' => 'required|boolean',
        ]);

        $this->findFinishing($id)
            ->updatePublicationStatus($request->input('is_published'));

        return redirect()->back();
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->findFinishing($id)->delete();

        return redirect()->route('finishings');
    }

    private function findFinishing(int $id): Finishing
    {
        return Finishing::withoutGlobalScope('published')
            ->with(['images'])
            ->findOrFail($id);
    }
}
