<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApiStatusDataRequest extends FormRequest
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
            'api_group' => ['nullable', 'string'],
            'method' => ['nullable', 'string'],
            'status' => ['nullable', 'string'],
            'response_time' => ['nullable'],
            'last_checked' => ['nullable'],
            'details' => ['nullable', 'string'],
        ];
    }
}
