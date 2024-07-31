<?php

namespace Database\Factories;

use App\Models\Organisation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
        $newUser = User::factory()->create();
        $newOrg = Organisation::factory()->create();
        $productName = $this->faker->word . "'s product";
        return [
            'user_id' => $newUser->id,
            'name' => $productName,
            'price' => $this->faker->randomFloat(2, 0, 1000),
            'slug' => $this->faker->unique()->slug,
            'tags' => $this->faker->word,
            'imageUrl' => $this->faker->imageUrl(),
            'status' => $this->faker->randomElement(['active', 'draft']),
            'quantity' => $this->faker->numberBetween(1, 100),
            'description' => $this->faker->text,
            'org_id' => $newOrg->org_id
        ];
    }
}
