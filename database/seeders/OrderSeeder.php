<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Support\Str;


class OrderSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $users = User::all();

        foreach (range(1, 50) as $index) {
            Order::create([
                'id' => Str::uuid(),
                'user_id' => $users->random()->id,
                'order_number' => $faker->unique()->numberBetween(100000, 999999),
                'total_amount' => $faker->randomFloat(2, 10, 1000),
                'tax' => $faker->randomFloat(2, 1, 100),
                'shipping_cost' => $faker->randomFloat(2, 5, 50),
                'discount' => $faker->randomFloat(2, 0, 50),
                'status' => $faker->randomElement(['pending', 'processing', 'shipped', 'delivered']),
                'payment_status' => $faker->randomElement(['unpaid', 'paid', 'refunded']),
                'payment_method' => $faker->randomElement(['credit_card', 'paypal', 'bank_transfer']),
                'shipping_address' => $faker->address,
                'billing_address' => $faker->address,
                'notes' => $faker->optional()->sentence,
            ]);
        }
    }
}
