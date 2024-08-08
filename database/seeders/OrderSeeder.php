<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Support\Str;


class OrderSeeder extends Seeder
{
    public function run()
    {
        // Order::factory()->count(10)->create();
    }
}
