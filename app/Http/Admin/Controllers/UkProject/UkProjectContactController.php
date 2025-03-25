<?php

namespace App\Http\Admin\Controllers\UkProject;

use App\Http\Admin\Controllers\Controller;
use App\Http\Admin\Requests\SaveUkProjectContactRequest;
use App\Models\Contact\Contact;
use App\Models\Contact\ContactType;
use App\Models\UkProject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class UkProjectContactController
 *
 * @package App\Http\Admin\Controllers\UkProject
 */
class UkProjectContactController extends Controller
{
    public function index(int $ukProjectId, Request $request): Response
    {
        $ukProject = $this->findUkProject($ukProjectId);

        return inertia('UkProjects/Contacts/List', [
            'contacts' => QueryBuilder::for(Contact::class)
                ->byUkProject($ukProject)
                ->allowedSorts(['title', 'type', 'created_at', 'updated_at'])
                ->paginate(),
            'defaultSorter' => $request->input('sort'),
            'ukProject' => $ukProject,
        ]);
    }

    public function create(int $ukProjectId): Response
    {
        return inertia('UkProjects/Contacts/Create', [
            'ukProject' => $this->findUkProject($ukProjectId),
            'contactTypes' => ContactType::toArray(),
        ]);
    }

    public function store(int $ukProjectId, SaveUkProjectContactRequest $request): RedirectResponse
    {
        /** @var Contact $contact */
        $contact = $this->findUkProject($ukProjectId)
            ->contacts()
            ->create($request->validated());

        return redirect()->route('uk-projects.contacts.edit', [
            'ukProjectId' => $ukProjectId,
            'id' => $contact->id,
        ]);
    }

    public function edit(int $ukProjectId, int $id): Response
    {
        $contact = $this->findContact($id);

        return inertia('UkProjects/Contacts/Edit', [
            'contact' => $contact,
            'contactTypes' => ContactType::toArray(),
            'ukProject' => $this->findUkProject($ukProjectId),
        ]);
    }

    public function update(
        int $ukProjectId,
        SaveUkProjectContactRequest $request,
        int $id
    ): RedirectResponse {
        $this->findContact($id)
            ->update($request->validated());

        return redirect()->route('uk-projects.contacts.edit', [
            'ukProjectId' => $ukProjectId,
            'id' => $id,
        ]);
    }

    public function destroy(int $ukProjectId, int $id): RedirectResponse
    {
        $this->findContact($id)->delete();

        return redirect()->route('uk-projects.contacts', [
            'ukProjectId' => $ukProjectId,
        ]);
    }

    public function sort(Request $request): RedirectResponse
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer',
        ]);

        Contact::setNewOrder($request->input('order'));

        return redirect()->back();
    }

    private function findUkProject(int $id): UkProject
    {
        return UkProject::withoutGlobalScope('published')
            ->with('image')
            ->findOrFail($id);
    }

    private function findContact(int $id): Contact
    {
        return Contact::with(['iconImage'])->findOrFail($id);
    }
}
