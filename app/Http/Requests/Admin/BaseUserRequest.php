<?php

namespace App\Http\Requests\Admin;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

abstract class BaseUserRequest extends BaseLocalizedRequest
{
    /**
     * @return array<int, string>
     */
    protected function roleOptions(): array
    {
        /** @var User|null $actor */
        $actor = $this->user();

        if ($actor?->isAdmin()) {
            return [User::ROLE_USER, User::ROLE_USER_PRO, User::ROLE_MANAGER, User::ROLE_ADMIN];
        }

        return [User::ROLE_USER, User::ROLE_USER_PRO, User::ROLE_MANAGER];
    }

    /**
     * @return array<int, mixed>
     */
    protected function organizationRule(): array
    {
        /** @var User|null $actor */
        $actor = $this->user();

        if ($actor?->isAdmin()) {
            return ['required', Rule::exists(Organization::class, 'org_id')];
        }

        return ['required', Rule::in([(string) $actor?->org_id])];
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function sharedUserRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'first_name' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'string', Rule::in(User::genderOptions())],
            'birth_date' => ['nullable', 'date'],
            'city' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', Rule::in(User::countryOptions())],
            'role' => ['required', 'string', Rule::in($this->roleOptions())],
            'org_id' => $this->organizationRule(),
        ];
    }

    /**
     * @return array<int, mixed>
     */
    protected function passwordRules(): array
    {
        return ['required', 'string', Password::defaults(), 'confirmed'];
    }
}
