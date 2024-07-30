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
                'price' => 0.00,
                'features' => json_encode(['Stage 1', 'Stage 2', 'Stage 3']),
                'description' => 'Free plan with basic features.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Premium',
                'price' => 300.00,
                'features' => json_encode(['Premium HNG', 'Premium Gen-z']),
                'description' => 'Premium plan with advanced features.',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
