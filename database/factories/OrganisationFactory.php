<?php

namespace Database\Factories;
use App\Models\User;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organisation>
 */
class OrganisationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'description' => $this->faker->text,
            'user_id' => User::factory(),
            'email' => $this->faker->unique()->companyEmail,
            'industry' => $this->faker->randomElement(['Technology', 'Finance', 'Healthcare', 'Education', 'Manufacturing', 'Retail', 'Agriculture', 'Entertainment']),
            'type' => $this->faker->randomElement(['Public', 'Private', 'Non-profit', 'Government']),
            'country' => $this->faker->country,
            'address' => $this->faker->address,
            'state' => $this->faker->state,
        ];
    }
}
