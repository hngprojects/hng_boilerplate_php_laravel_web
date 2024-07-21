<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'current_password' => 'required|string',
            'new_password' => [
                'required',
                'string',
                'min:8', // Minimum 8 characters
                Password::min(8) // Use Laravel's Password facade for additional rules
                    ->mixedCase() // Require at least one uppercase and one lowercase letter
                    ->letters() // Require at least one letter
                    ->numbers() // Require at least one number
                    ->symbols() // Require at least one symbol
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'current_password.required' => 'Current password is required',
            'new_password.required' => 'New password is required',
            'new_password.min' => 'New password must be at least 8 characters',
            'new_password.mixedCase' => 'New password must include at least one uppercase and one lowercase letter',
            'new_password.letters' => 'New password must include at least one letter',
            'new_password.numbers' => 'New password must include at least one number',
            'new_password.symbols' => 'New password must include at least one symbol',
        ];
    }
}
