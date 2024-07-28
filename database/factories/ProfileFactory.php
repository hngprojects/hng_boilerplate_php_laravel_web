<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Profile>
 */
class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $first_name = $this->faker->firstName;
        $last_name = $this->faker->lastName;
        $newUser = User::factory()->make();

        return [
            'user_id' => $newUser->id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'phone' => $this->faker->phoneNumber,
            'avatar_url' => $this->faker->imageUrl(),
        ];
    }
}
