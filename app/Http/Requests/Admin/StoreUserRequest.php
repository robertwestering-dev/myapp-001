<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Validation\Rule;

class StoreUserRequest extends BaseUserRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canAccessAdminPortal() ?? false;
    }

    public function rules(): array
    {
        return [
            ...$this->sharedUserRules(),
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'password' => $this->passwordRules(),
        ];
    }
}
