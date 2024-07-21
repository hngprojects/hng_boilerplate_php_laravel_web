<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $newUser = User::factory()->make();
        return [
            'user_id' => $newUser->id,
            'name' => $this->faker->word . "'s product",
            'description' => $this->faker->text,
        ];
    }
}
