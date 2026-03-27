<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
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
        return view('admin.users.form', [
            'title' => 'Nieuwe gebruiker',
            'intro' => 'Voeg een nieuwe gebruiker toe aan de applicatie vanuit de admin-omgeving.',
            'submitLabel' => 'Gebruiker toevoegen',
            'user' => new User(['role' => User::ROLE_USER]),
            'isEditing' => false,
            'roles' => [User::ROLE_USER, User::ROLE_ADMIN],
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        User::create($request->validated());

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Gebruiker succesvol toegevoegd.');
    }

    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->value();

        $users = $this->usersQuery($search)
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', [
            'search' => $search,
            'users' => $users,
        ]);
    }

    public function edit(User $user): View
    {
        return view('admin.users.form', [
            'title' => 'Gebruiker wijzigen',
            'intro' => 'Werk de gegevens en rol van deze gebruiker bij.',
            'submitLabel' => 'Wijzigingen opslaan',
            'user' => $user,
            'isEditing' => true,
            'roles' => [User::ROLE_USER, User::ROLE_ADMIN],
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $user->update($request->validated());

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Gebruiker succesvol bijgewerkt.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Gebruiker succesvol verwijderd.');
    }

    public function export(Request $request): StreamedResponse
    {
        $search = $request->string('search')->trim()->value();
        $fileName = 'users.csv';

        return response()->streamDownload(function () use ($search): void {
            $handle = fopen('php://output', 'w');

            if ($handle === false) {
                return;
            }

            fputcsv($handle, ['Naam', 'Emailadres', 'Rol', 'Email verified']);

            $this->usersQuery($search)
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

    protected function usersQuery(string $search): Builder
    {
        return User::query()
            ->select(['id', 'name', 'email', 'role', 'email_verified_at'])
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('name');
    }
}
