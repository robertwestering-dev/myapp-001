<?php

namespace App\Http\Requests\Admin;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrganizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var User|null $actor */
        $actor = $this->user();
        $organization = $this->route('organization');

        return $actor !== null
            && $organization instanceof Organization
            && $actor->canManageOrganization($organization->org_id);
    }

    public function rules(): array
    {
        /** @var User|null $actor */
        $actor = $this->user();

        $contactRule = $actor?->isAdmin()
            ? Rule::exists(User::class, 'id')->whereIn('role', [User::ROLE_ADMIN, User::ROLE_MANAGER])
            : Rule::exists(User::class, 'id')
                ->where('org_id', $actor?->org_id)
                ->whereIn('role', [User::ROLE_ADMIN, User::ROLE_MANAGER]);

        return [
            'naam' => ['required', 'string', 'max:255'],
            'adres' => ['required', 'string', 'max:255'],
            'postcode' => ['required', 'string', 'max:20'],
            'plaats' => ['required', 'string', 'max:255'],
            'land' => ['required', 'string', Rule::in(Organization::countryOptions())],
            'telefoon' => ['required', 'string', 'max:30'],
            'contact_id' => ['required', $contactRule],
        ];
    }
}
