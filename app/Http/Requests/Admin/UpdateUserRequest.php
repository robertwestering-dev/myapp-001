<?php

namespace App\Http\Requests\Admin;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var User $user */
        $user = $this->route('user');
        /** @var User|null $actor */
        $actor = $this->user();

        $roles = $actor?->isAdmin()
            ? [User::ROLE_USER, User::ROLE_MANAGER, User::ROLE_ADMIN]
            : [User::ROLE_USER, User::ROLE_MANAGER];

        $organizationRule = $actor?->isAdmin()
            ? Rule::exists(Organization::class, 'org_id')
            : Rule::in([(string) $actor?->org_id]);

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($user)],
            'role' => ['required', 'string', Rule::in($roles)],
            'org_id' => ['required', $organizationRule],
        ];
    }
}
