<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserController extends Controller
{
    public function create(): View
    {
        /** @var User $actor */
        $actor = request()->user();

        return view('admin.users.form', [
            'title' => 'Nieuwe gebruiker',
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

        User::create($attributes);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Gebruiker succesvol toegevoegd.');
    }

    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->value();
        /** @var User $actor */
        $actor = $request->user();

        $users = $this->usersQuery($actor, $search)
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', [
            'search' => $search,
            'users' => $users,
        ]);
    }

    public function edit(User $user): View
    {
        /** @var User $actor */
        $actor = request()->user();

        abort_unless($actor->canManageOrganization($user->org_id), 403);

        return view('admin.users.form', [
            'title' => 'Gebruiker wijzigen',
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

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Gebruiker succesvol bijgewerkt.');
    }

    public function confirmDestroy(User $user): View
    {
        /** @var User $actor */
        $actor = request()->user();

        abort_unless($actor->canManageOrganization($user->org_id), 403);

        return view('admin.users.confirm-delete', [
            'user' => $user,
        ]);
    }

    public function destroy(User $user): RedirectResponse
    {
        /** @var User $actor */
        $actor = request()->user();

        abort_unless($actor->canManageOrganization($user->org_id), 403);

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Gebruiker succesvol verwijderd.');
    }

    public function export(Request $request): StreamedResponse
    {
        $search = $request->string('search')->trim()->value();
        $fileName = 'users.csv';
        /** @var User $actor */
        $actor = $request->user();

        return response()->streamDownload(function () use ($actor, $search): void {
            $handle = fopen('php://output', 'w');

            if ($handle === false) {
                return;
            }

            fputcsv($handle, ['Naam', 'Emailadres', 'Rol', 'Email verified']);

            $this->usersQuery($actor, $search)
                ->cursor()
                ->each(function (User $user) use ($handle): void {
                    fputcsv($handle, [
                        $user->name,
                        $user->email,
                        $user->role,
                        $user->email_verified_at?->toDateTimeString() ?? '',
                    ]);
                });

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    protected function usersQuery(User $actor, string $search): Builder
    {
        return User::query()
            ->select(['id', 'name', 'email', 'role', 'org_id', 'email_verified_at'])
            ->when(! $actor->isAdmin(), function (Builder $query) use ($actor): void {
                $query->where('org_id', $actor->org_id);
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
    protected function organizationOptions(User $actor): array
    {
        return Organization::query()
            ->when(! $actor->isAdmin(), function (Builder $query) use ($actor): void {
                $query->where('org_id', $actor->org_id);
            })
            ->orderBy('naam')
            ->pluck('naam', 'org_id')
            ->all();
    }

    /**
     * @return array<int, string>
     */
    protected function roleOptions(User $actor): array
    {
        if ($actor->isAdmin()) {
            return [User::ROLE_USER, User::ROLE_MANAGER, User::ROLE_ADMIN];
        }

        return [User::ROLE_USER, User::ROLE_MANAGER];
    }
}
