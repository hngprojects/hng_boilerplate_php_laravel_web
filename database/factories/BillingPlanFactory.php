<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BillingPlan>
 */
class BillingPlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    
    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'name' => $this->faker->words(3, true),
            'price' => $this->faker->randomFloat(2, 10, 1000), // Generates a random price between $10 and $1000
            'features' => json_encode($this->faker->words(5)), // Generates a JSON array of 5 random words
            'description' => $this->faker->sentence, // Generates a random sentence
        ];
        
    }
}
