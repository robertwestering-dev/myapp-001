<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends BaseUserRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canAccessAdminPortal() ?? false;
    }

    public function rules(): array
    {
        /** @var User $user */
        $user = $this->route('user');

        return [
            ...$this->sharedUserRules(),
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($user)],
        ];
    }
}
