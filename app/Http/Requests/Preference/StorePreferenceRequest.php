<?php

namespace App\Http\Requests\Preference;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StorePreferenceRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = Auth::user();

        return [
            'name' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($user) {
                    if ($user->preferences()->where('name', $value)->exists()) {
                        $fail("The $attribute already exists.");
                    }
                },
            ],
            'value' => 'required|string',
        ];
    }
}
