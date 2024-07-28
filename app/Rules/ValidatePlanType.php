<?php

namespace App\Rules;

use App\Enums\PlanType;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidatePlanType implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $plans = implode(', ', array_column(PlanType::cases(), 'value'));
        if (!in_array($value, array_column(PlanType::cases(), 'value'))) {
            $fail("The :attribute plan name not allowed. Allowed plans are $plans");
        }
    }
}
