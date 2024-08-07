<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $orderNumber = $this->generateUniqueOrderNumber();

        return [
            'user_id' => User::factory(),
            'product_id' => Product::factory(),
            'status' => $this->faker->boolean,
            'quantity' => $this->faker->randomDigit(),
            'total_amount' => $this->faker->randomDigit(),
            'order_number' => $orderNumber,
        ];
    }

    protected function generateUniqueOrderNumber()
    {
        $unique = false;
        $orderNumber = null;

        while (!$unique) {
            // Generate a random order number (e.g., 8 digits)
            $orderNumber = $this->faker->unique()->regexify('[A-Za-z0-9]{8}');

            // Check if the generated order number is unique in the database
            if (!DB::table('orders')->where('order_number', $orderNumber)->exists()) {
                $unique = true;
            }
        }

        return $orderNumber;
    }
}
