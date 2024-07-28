<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlogCreateRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'images' => ['required'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,gif,svg', 'max:500'],
            'tags' => ['required', 'array'],
            'tags.*.title' => ['string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
        ];
    }
}
