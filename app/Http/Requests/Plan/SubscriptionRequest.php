<?php

namespace App\Http\Requests\Plan;

use App\Enums\PlanType;
use App\Rules\ValidatePlanType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubscriptionRequest extends FormRequest
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
            'name' =>  ['required',  new ValidatePlanType],
            'duration' => 'required|string|in:monthly,yearly',
            'price' => 'integer|required',
            'description' => 'required|string',
            'features' => 'array|required',
            'features.*.id' => 'uuid|required|exists:features,id',
            'features.*.status' => 'int|required|in:0,1'
        ];
    }

    public function attributes(): array
    {
        return [
            'features.*.id' => 'feature'
        ];
    }
}
