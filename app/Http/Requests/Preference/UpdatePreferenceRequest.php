<?php

namespace App\Http\Requests\Preference;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdatePreferenceRequest extends FormRequest
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
        $id = $this->route('id');
        $user = Auth::user();

        return [
            'name' => [
                'required',
                'string',
                'unique:preferences,name,' . $id,
                function ($attribute, $value, $fail) use ($user) {
                    if ($user->preferences()->where('name', $value)->doesntExist()) {
                        $fail("$attribute does not exists.");
                    }
                },
            ],
            'value' => 'required|string',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $id = $this->route('id');
            $user = Auth::user();

            if (!is_string($id) || !$user->preferences()->where('id', $id)->exists()) {
                $validator->errors()->add('id', 'The selected id is invalid.');
            }
        });
    }
}
