<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrganizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var User|null $actor */
        $actor = $this->user();

        $contactRule = $actor?->isAdmin()
            ? Rule::exists(User::class, 'id')
            : Rule::exists(User::class, 'id')->where('org_id', $actor?->org_id);

        return [
            'naam' => ['required', 'string', 'max:255'],
            'adres' => ['required', 'string', 'max:255'],
            'postcode' => ['required', 'string', 'max:20'],
            'plaats' => ['required', 'string', 'max:255'],
            'land' => ['required', 'string', 'max:255'],
            'telefoon' => ['required', 'string', 'max:30'],
            'contact_id' => ['required', $contactRule],
        ];
    }
}
