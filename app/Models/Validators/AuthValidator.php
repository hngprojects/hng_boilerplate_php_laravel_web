<?php

namespace App\Models\Validators;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AuthValidator
{
    public function validate(User $user, array $attributes): array
    {
        return validator($attributes,
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:users'],
                'password' => ['required', Password::min(8)],
            ]
        )->validate();
    }
}