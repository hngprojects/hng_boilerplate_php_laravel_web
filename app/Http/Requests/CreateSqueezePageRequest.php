<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;


class CreateSqueezePageRequest extends FormRequest
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
            'slug' => 'nullable|string|max:255|unique:squeeze_pages,slug',
            'status' => 'required|in:offline,online',
            'activate' => 'required|boolean',
            'headline' => 'required|string|max:255',
            'sub_headline' => 'required|string|max:255',
            'hero_image' => 'required|string|max:255',
            'content' => 'required|string',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();

        $formattedError = [];

        foreach ($errors as $field => $messages) {
            foreach ($messages as $message) {
                $formattedError[] = [
                    "field" => $field,
                    "message" => $message
                ];
            }
        }

        throw new HttpResponseException(response()->json([
            'status_code' => Response::HTTP_BAD_REQUEST,
            'errors' => $formattedError,
        ], Response::HTTP_BAD_REQUEST));
    }
}
