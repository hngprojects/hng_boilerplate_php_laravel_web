<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoleRequest extends FormRequest
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
        return [
            'name' => [
                'required',
                'string',
                'max:255'
            ],
            'description' => [
                'nullable',
                'string',
                'max:255',
            ],
            'permissions' => [
                'required',
                'array',
            ],
            'permissions.*' => [
                'uuid', 
                'exists:permissions,id',
            ],
        ];
    }

    public function messages()
    {
        return [
            'permissions.array' => 'Permissions must be provided as an array.',
            'permissions.*.uuid' => 'Each permission ID must be a valid UUID.',
            'permissions.*.exists' => 'Each permission ID must be valid.',
        ];
    }
}
