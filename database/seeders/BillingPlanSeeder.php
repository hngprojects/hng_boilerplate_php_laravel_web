<?php

namespace Database\Seeders;

use App\Models\BillingPlan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BillingPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BillingPlan::insert([
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Free',
                'price' => 0,
                'features' => json_encode(['Stage 1', 'Stage 2', 'Stage 3']),
                'description' => 'Free plan with basic features.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Basic',
                'price' => 20,
                'features' => json_encode(['100 projects', 'up to 50 subscribers', 'Advanced analytics', '24-hour support']),
                'description' => 'Ideal for growing needs who want more features',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Advanced',
                'price' => 50,
                'features' => json_encode(['200 projects', 'up to 100 subscribers', 'Advanced analytics', '24-hour support', 'Marketing advisor']),
                'description' => 'Designed for power users and maximum functionalities',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Premium',
                'price' => 100,
                'features' => json_encode(['300 projects', 'up to 500 subscribers', 'Advanced analytics', '24-hour support', 'Marketing advisor']),
                'description' => 'Perfect for users who want more features',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
