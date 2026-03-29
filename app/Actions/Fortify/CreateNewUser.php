<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Mail\NewAccountRegistered;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'password' => $this->passwordRules(),
        ])->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'role' => User::ROLE_USER,
            'password' => $input['password'],
        ]);

        Mail::to(
            config('contact.new_account_recipient_address'),
            config('contact.new_account_recipient_name'),
        )->send(new NewAccountRegistered(
            name: $user->name,
            email: $user->email,
        ));

        return $user;
    }
}
