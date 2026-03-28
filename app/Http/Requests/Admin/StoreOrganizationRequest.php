<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrganizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'naam' => ['required', 'string', 'max:255'],
            'adres' => ['required', 'string', 'max:255'],
            'postcode' => ['required', 'string', 'max:20'],
            'plaats' => ['required', 'string', 'max:255'],
            'land' => ['required', 'string', 'max:255'],
            'telefoon' => ['required', 'string', 'max:30'],
            'contact_id' => ['required', Rule::exists(User::class, 'id')],
        ];
    }
}
