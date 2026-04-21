<?php

namespace App\Http\Controllers\Admin;

use App\Concerns\ProvidesOrganizationOptions;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\Organization;
use App\Models\User;
use App\Services\AuditLogger;
use App\Support\CsvExporter;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserController extends Controller
{
    use ProvidesOrganizationOptions;

    public function __construct(private readonly AuditLogger $audit) {}

    public function create(Request $request): View
    {
        /** @var User $actor */
        $actor = $request->user();

        return view('admin.users.form', [
            'title' => __('hermes.admin.form_titles.new_user'),
            'intro' => 'Voeg een nieuwe gebruiker toe aan de applicatie vanuit de admin-omgeving.',
            'submitLabel' => 'Gebruiker toevoegen',
            'user' => new User(['role' => User::ROLE_USER]),
            'isEditing' => false,
            'organizations' => $this->organizationOptions($actor),
            'roles' => $this->roleOptions($actor),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        /** @var User $actor */
        $actor = $request->user();

        $attributes = $request->validated();

        if (! $actor->isAdmin()) {
            $attributes['org_id'] = $actor->org_id;
        }

        $user = User::create($attributes);

        $user->sendEmailVerificationNotification();

        $this->audit->log('user.created', "Gebruiker aangemaakt: {$user->name} ({$user->email})", $user);

        return redirect()
            ->route('admin.users.index')
            ->with('status', __('hermes.admin.users.created'));
    }

    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->value();
        $organization = $request->string('organization')->trim()->value();
        $role = $request->string('role')->trim()->value();
        $country = $request->string('country')->trim()->value();
        /** @var User $actor */
        $actor = $request->user();

        $organizations = $this->organizationOptions($actor);

        $users = $this->usersQuery($actor, $search, $organization, $role, $country)
            ->with('organization:org_id,naam')
            ->paginate(config('app.per_page'))
            ->withQueryString();

        return view('admin.users.index', [
            'search' => $search,
            'selectedOrganization' => $organization,
            'selectedRole' => $role,
            'selectedCountry' => $country,
            'organizations' => $organizations,
            'roles' => $this->roleOptions($actor),
            'countries' => User::countryOptions(),
            'activeFilters' => $this->activeFilters($search, $organization, $role, $country, $organizations),
            'users' => $users,
        ]);
    }

    public function edit(Request $request, User $user): View
    {
        /** @var User $actor */
        $actor = $request->user();

        abort_unless($actor->canManageOrganization($user->org_id), 403);

        return view('admin.users.form', [
            'title' => __('hermes.admin.form_titles.edit_user'),
            'intro' => 'Werk de gegevens en rol van deze gebruiker bij.',
            'submitLabel' => 'Wijzigingen opslaan',
            'user' => $user,
            'isEditing' => true,
            'organizations' => $this->organizationOptions($actor),
            'roles' => $this->roleOptions($actor),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        /** @var User $actor */
        $actor = $request->user();

        abort_unless($actor->canManageOrganization($user->org_id), 403);

        $attributes = $request->validated();

        if (! $actor->isAdmin()) {
            $attributes['org_id'] = $actor->org_id;
        }

        $user->update($attributes);

        $this->audit->log('user.updated', "Gebruiker bijgewerkt: {$user->name} ({$user->email})", $user);

        return redirect()
            ->route('admin.users.index')
            ->with('status', __('hermes.admin.users.updated'));
    }

    public function confirmDestroy(Request $request, User $user): View
    {
        /** @var User $actor */
        $actor = $request->user();

        abort_unless($actor->canManageOrganization($user->org_id), 403);

        return view('admin.users.confirm-delete', [
            'user' => $user,
        ]);
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        /** @var User $actor */
        $actor = $request->user();

        abort_unless($actor->canManageOrganization($user->org_id), 403);

        $organizationUsingUserAsContact = Organization::query()
            ->select(['org_id', 'naam'])
            ->where('contact_id', $user->id)
            ->first();

        if ($organizationUsingUserAsContact !== null) {
            return redirect()
                ->route('admin.users.index')
                ->withErrors([
                    'user' => __('hermes.admin.users.delete_is_contact', [
                        'organization' => $organizationUsingUserAsContact->naam,
                    ]),
                ]);
        }

        $this->audit->log('user.deleted', "Gebruiker verwijderd: {$user->name} ({$user->email})", $user);

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('status', __('hermes.admin.users.deleted'));
    }

    public function export(Request $request): StreamedResponse
    {
        $search = $request->string('search')->trim()->value();
        $organization = $request->string('organization')->trim()->value();
        $role = $request->string('role')->trim()->value();
        $country = $request->string('country')->trim()->value();
        $fileName = 'users.csv';
        /** @var User $actor */
        $actor = $request->user();

        return response()->streamDownload(function () use ($actor, $search, $organization, $role, $country): void {
            $csv = (new CsvExporter)->open();

            if (! $csv->isOpen()) {
                return;
            }

            $csv->writeRow(['Naam', 'Emailadres', 'Rol', 'Email verified']);

            $this->usersQuery($actor, $search, $organization, $role, $country)
                ->cursor()
                ->each(function (User $user) use ($csv): void {
                    $csv->writeRow([
                        $user->name,
                        $user->email,
                        $user->role,
                        $user->email_verified_at?->toDateTimeString() ?? '',
                    ]);
                });

            $csv->close();
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    protected function usersQuery(User $actor, string $search, string $organization, string $role, string $country): Builder
    {
        return User::query()
            ->select(['id', 'name', 'email', 'role', 'org_id', 'email_verified_at'])
            ->when(! $actor->isAdmin(), function (Builder $query) use ($actor): void {
                $query->where('org_id', $actor->org_id);
            })
            ->when($organization !== '', function (Builder $query) use ($organization): void {
                $query->where('org_id', $organization);
            })
            ->when($role !== '', function (Builder $query) use ($role): void {
                $query->where('role', $role);
            })
            ->when($country !== '', function (Builder $query) use ($country): void {
                $query->where('country', $country);
            })
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('name');
    }

    /**
     * @return array<int, string>
     */
    protected function roleOptions(User $actor): array
    {
        if ($actor->isAdmin()) {
            return [User::ROLE_USER, User::ROLE_USER_PRO, User::ROLE_MANAGER, User::ROLE_ADMIN];
        }

        return [User::ROLE_USER, User::ROLE_USER_PRO, User::ROLE_MANAGER];
    }

    /**
     * @param  array<int, string>  $organizations
     * @return array<int, string>
     */
    protected function activeFilters(string $search, string $organization, string $role, string $country, array $organizations): array
    {
        $filters = [];

        if ($search !== '') {
            $filters[] = 'Zoekterm: '.$search;
        }

        if ($organization !== '') {
            $organizationName = $organizations[$organization] ?? null;

            if ($organizationName !== null) {
                $filters[] = 'Organisatie: '.$organizationName;
            }
        }

        if ($role !== '') {
            $filters[] = 'Rol: '.$role;
        }

        if ($country !== '') {
            $filters[] = 'Land: '.$country;
        }

        return $filters;
    }
}
