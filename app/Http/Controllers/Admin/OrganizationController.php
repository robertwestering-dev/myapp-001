<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreOrganizationRequest;
use App\Http\Requests\Admin\UpdateOrganizationRequest;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function index(): View
    {
        /** @var User $actor */
        $actor = request()->user();

        $organizations = Organization::query()
            ->with('contact:id,name')
            ->when(! $actor->isAdmin(), function ($query) use ($actor): void {
                $query->where('org_id', $actor->org_id);
            })
            ->orderBy('naam')
            ->paginate(15);

        return view('admin.organizations.index', [
            'canCreateOrganizations' => $actor->isAdmin(),
            'canDeleteOrganizations' => $actor->isAdmin(),
            'organizations' => $organizations,
        ]);
    }

    public function create(): View
    {
        /** @var User $actor */
        $actor = request()->user();

        abort_unless($actor->isAdmin(), 403);

        return view('admin.organizations.form', [
            'title' => 'Nieuwe organisatie',
            'intro' => 'Voeg een nieuwe organisatie toe en koppel direct een contactpersoon.',
            'submitLabel' => 'Organisatie toevoegen',
            'organization' => new Organization,
            'contacts' => $this->contactOptions($actor),
            'isEditing' => false,
        ]);
    }

    public function store(StoreOrganizationRequest $request): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        Organization::create($request->validated());

        return redirect()
            ->route('admin.organizations.index')
            ->with('status', 'Organisatie succesvol toegevoegd.');
    }

    public function edit(Organization $organization): View
    {
        /** @var User $actor */
        $actor = request()->user();

        abort_unless($actor->canManageOrganization($organization->org_id), 403);

        return view('admin.organizations.form', [
            'title' => 'Organisatie wijzigen',
            'intro' => 'Werk de organisatiegegevens en de contactpersoon bij.',
            'submitLabel' => 'Wijzigingen opslaan',
            'organization' => $organization,
            'contacts' => $this->contactOptions($actor),
            'isEditing' => true,
        ]);
    }

    public function update(UpdateOrganizationRequest $request, Organization $organization): RedirectResponse
    {
        /** @var User $actor */
        $actor = $request->user();

        abort_unless($actor->canManageOrganization($organization->org_id), 403);

        $organization->update($request->validated());

        return redirect()
            ->route('admin.organizations.index')
            ->with('status', 'Organisatie succesvol bijgewerkt.');
    }

    public function confirmDestroy(Organization $organization): View
    {
        abort_unless(request()->user()?->isAdmin(), 403);

        return view('admin.organizations.confirm-delete', [
            'organization' => $organization->load('contact:id,name,email'),
        ]);
    }

    public function destroy(Request $request, Organization $organization): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        if ($organization->naam === 'Hermes Results') {
            return redirect()
                ->route('admin.organizations.index')
                ->withErrors(['organization' => 'Hermes Results is de standaardorganisatie en kan niet worden verwijderd.']);
        }

        if ($organization->users()->exists()) {
            return redirect()
                ->route('admin.organizations.index')
                ->withErrors(['organization' => 'Deze organisatie heeft nog gekoppelde gebruikers en kan daarom niet worden verwijderd.']);
        }

        $organization->delete();

        return redirect()
            ->route('admin.organizations.index')
            ->with('status', 'Organisatie succesvol verwijderd.');
    }

    /**
     * @return array<int, string>
     */
    protected function contactOptions(User $actor): array
    {
        return User::query()
            ->when(! $actor->isAdmin(), function ($query) use ($actor): void {
                $query->where('org_id', $actor->org_id);
            })
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }
}
