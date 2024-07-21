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
            'role_name' => [
                'required',
                'string',
                'max:255'
            ],
            'organisation_id' => [
                'required',
                'string',
                'max:255',
                'exists:organisations,org_id',
                Rule::unique('roles', 'org_id')->where('name', $this->input('role_name'))
            ],
            'permissions_id' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'organisation_id.unique'=> "'".$this->input('role_name')."' role has already been created for the organisation",
        ];
    }
}
