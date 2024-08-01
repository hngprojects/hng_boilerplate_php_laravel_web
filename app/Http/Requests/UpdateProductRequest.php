<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'image' => 'nullable|url',
            'is_archived' => 'sometimes|boolean',
            'productsVariant' => 'required|array',
            'productsVariant.*.size_id' => 'required|uuid|exists:sizes,id',
            'productsVariant.*.stock' => 'required|integer|min:0',
            'productsVariant.*.price' => 'required|numeric|min:0',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'productsVariant.*.size_id' => 'size ID',
            'productsVariant.*.stock' => 'stock quantity',
            'productsVariant.*.price' => 'price',
        ];
    }
}
