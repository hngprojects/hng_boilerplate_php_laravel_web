<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Job>
 */
class JobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->jobTitle,
            'description' => $this->faker->paragraph,
            'location' => $this->faker->city,
            'salary' => $this->faker->numberBetween(50000, 150000),
            'job_type' => $this->faker->randomElement(['Full-time', 'Part-time', 'Contract']),
            'company_name' => $this->faker->company,
        ];
    }
}
