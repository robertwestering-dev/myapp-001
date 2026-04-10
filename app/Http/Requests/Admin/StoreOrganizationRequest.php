<?php

namespace App\Http\Requests\Admin;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrganizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'naam' => ['required', 'string', 'max:255'],
            'adres' => ['required', 'string', 'max:255'],
            'postcode' => ['required', 'string', 'max:20'],
            'plaats' => ['required', 'string', 'max:255'],
            'land' => ['required', 'string', Rule::in(Organization::countryOptions())],
            'telefoon' => ['required', 'string', 'max:30'],
            'contact_id' => ['required', Rule::exists(User::class, 'id')->whereIn('role', [User::ROLE_ADMIN, User::ROLE_MANAGER])],
        ];
    }
}
