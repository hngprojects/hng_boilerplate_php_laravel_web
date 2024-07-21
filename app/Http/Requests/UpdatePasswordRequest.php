<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
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
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        throw new HttpResponseException(
            response()->json([
                'status' => 'unsuccessful',
                'message' => $errors->first(),
            ], 400)
        );
    }
}
