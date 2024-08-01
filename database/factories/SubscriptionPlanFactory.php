<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubscriptionPlan>
 */
class SubscriptionPlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'name' => $this->faker->randomElement(['basic', 'premium']),
            // 'price' => $this->faker->numberBetween(1000, 2000),
            // 'duration' => $this->faker->randomElement(['monthly', 'yearly']),
            // 'description' => $this->faker->realText()
        ];
    }
}
