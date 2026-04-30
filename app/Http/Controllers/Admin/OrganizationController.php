<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AuditAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreOrganizationRequest;
use App\Http\Requests\Admin\UpdateOrganizationRequest;
use App\Models\Organization;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function __construct(private readonly AuditLogger $audit) {}

    public function index(Request $request): View
    {
        /** @var User $actor */
        $actor = $request->user();

        $organizations = Organization::query()
            ->with('contact:id,name')
            ->forActor($actor)
            ->orderBy('naam')
            ->paginate(config('app.per_page'))
            ->withQueryString();

        return view('admin.organizations.index', [
            'canCreateOrganizations' => $actor->isAdmin(),
            'canDeleteOrganizations' => $actor->isAdmin(),
            'organizations' => $organizations,
        ]);
    }

    public function create(Request $request): View
    {
        /** @var User $actor */
        $actor = $request->user();

        abort_unless($actor->isAdmin(), 403);

        return view('admin.organizations.form', [
            'title' => __('hermes.admin.form_titles.new_organization'),
            'intro' => 'Voeg een nieuwe organisatie toe en koppel direct een contactpersoon.',
            'submitLabel' => 'Organisatie toevoegen',
            'organization' => new Organization,
            'contacts' => $this->contactOptions($actor),
            'isEditing' => false,
        ]);
    }

    public function store(StoreOrganizationRequest $request): RedirectResponse
    {
        $organization = Organization::create($request->validated());

        $this->audit->log(AuditAction::OrganizationCreated, "Organisatie aangemaakt: {$organization->naam}", $organization);

        return redirect()
            ->route('admin.organizations.index')
            ->with('status', __('hermes.admin.organizations.created'));
    }

    public function edit(Request $request, Organization $organization): View
    {
        /** @var User $actor */
        $actor = $request->user();

        abort_unless($actor->canManageOrganization($organization->org_id), 403);

        return view('admin.organizations.form', [
            'title' => __('hermes.admin.form_titles.edit_organization'),
            'intro' => 'Werk de organisatiegegevens en de contactpersoon bij.',
            'submitLabel' => 'Wijzigingen opslaan',
            'organization' => $organization,
            'contacts' => $this->contactOptions($actor),
            'isEditing' => true,
        ]);
    }

    public function update(UpdateOrganizationRequest $request, Organization $organization): RedirectResponse
    {
        $organization->update($request->validated());

        $this->audit->log(AuditAction::OrganizationUpdated, "Organisatie bijgewerkt: {$organization->naam}", $organization);

        return redirect()
            ->route('admin.organizations.index')
            ->with('status', __('hermes.admin.organizations.updated'));
    }

    public function confirmDestroy(Request $request, Organization $organization): View
    {
        /** @var User $actor */
        $actor = $request->user();
        abort_unless($actor->isAdmin(), 403);

        return view('admin.organizations.confirm-delete', [
            'organization' => $organization->load('contact:id,name,email'),
        ]);
    }

    public function destroy(Request $request, Organization $organization): RedirectResponse
    {
        /** @var User $actor */
        $actor = $request->user();
        abort_unless($actor->isAdmin(), 403);

        if ($organization->naam === config('app.protected_organization')) {
            return redirect()
                ->route('admin.organizations.index')
                ->withErrors(['organization' => __('hermes.admin.organizations.delete_protected')]);
        }

        if ($organization->users()->exists()) {
            return redirect()
                ->route('admin.organizations.index')
                ->withErrors(['organization' => __('hermes.admin.organizations.delete_has_users')]);
        }

        $this->audit->log(AuditAction::OrganizationDeleted, "Organisatie verwijderd: {$organization->naam}", $organization);

        $organization->delete();

        return redirect()
            ->route('admin.organizations.index')
            ->with('status', __('hermes.admin.organizations.deleted'));
    }

    /**
     * @return array<int, string>
     */
    protected function contactOptions(User $actor): array
    {
        return User::query()
            ->whereIn('role', [User::ROLE_ADMIN, User::ROLE_MANAGER])
            ->when(! $actor->isAdmin(), function ($query) use ($actor): void {
                $query->where('org_id', $actor->org_id);
            })
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }
}
