<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Size;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductVariantFactory extends Factory
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
            'stock' => $this->faker->numberBetween(0, 100),
            'stock_status' => $this->faker->randomElement(['in_stock', 'out_of_stock']),
            'price' => $this->faker->numberBetween(100, 1000),
            'size_id' => Size::factory(),
        ];
    }
}
