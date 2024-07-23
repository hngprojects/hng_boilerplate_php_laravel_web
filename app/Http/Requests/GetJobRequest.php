<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetJobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // We're using JWT middleware for authorization, so we can return true here
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required|string|exists:jobs,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'id.required' => 'A job ID is required',
            'id.string' => 'The job ID must be a string',
            'id.exists' => 'The specified job does not exist',
        ];
    }
}
