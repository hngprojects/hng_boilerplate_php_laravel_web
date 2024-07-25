<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class UpdateJobRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'salary' => 'required|string|max:255',
            'job_type' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = [
            'status_code' => Response::HTTP_BAD_REQUEST,
            'error' => 'Bad request',
            'message' => $validator->errors()->all()
        ];

        throw new HttpResponseException(response()->json($response, Response::HTTP_BAD_REQUEST));
    }
}
