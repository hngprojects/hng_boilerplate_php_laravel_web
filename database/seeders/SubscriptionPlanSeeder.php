<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;
use Carbon\Carbon;
use Illuminate\Support\Str;


class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        {
            SubscriptionPlan::insert([
                [
                    'id' => Str::uuid()->toString(),
                    "name" => "Free",
                    "price" => 0,
                    "duration" => "monthly",
                    "description" => "Ideal for individuals and small teams starting out.",
                    "created_at" => Carbon::now(),
                    "features" => json_encode([
                        "10 Projects",
                        "Up to 10 subscribers",
                        "Advanced analytics"
                    ]),
                    "paystack_plan_code" => null,
                    "flutterwave_plan_code" => null,
                ],
                [
                    'id' => Str::uuid()->toString(),
                    "name" => "Basic",
                    "price" => 20,
                    "duration" => "monthly",
                    "description" => "Perfect for growing businesses needing more resources.",
                    "paystack_plan_code" => "PLN_evl4zpdsa7o609r",
                    "flutterwave_plan_code" => "124672",
                    "created_at" => Carbon::now(),
                    "features" => json_encode([
                        "100 Projects",
                        "Up to 50 subscribers",
                        "Advanced analytics",
                        "24-hour support"
                    ])
                ],
                [
                    'id' => Str::uuid()->toString(),
                    "name" => "Advanced",
                    "duration" => "monthly",
                    "description" => "Designed for scaling businesses with complex needs.",
                    "price" => 50,
                    "paystack_plan_code" => "PLN_4baw6rztao7uhuj",
                    "flutterwave_plan_code" => "124673",
                    "created_at" => Carbon::now(),
                    "features" => json_encode([
                        "200 Projects",
                        "Up to 100 subscribers",
                        "Advanced analytics",
                        "24-hour support",
                        "Marketing advisor"
                    ])
                ],
                [
                    'id' => Str::uuid()->toString(),
                    "name" => "Premium",
                    "price" => 100,
                    "duration" => "monthly",
                    "paystack_plan_code" => "PLN_gz05ggbxp6dnevn",
                    "flutterwave_plan_code" => "124674",
                    "description" => "Ultimate plan for large enterprises with extensive requirements.",
                    "created_at" => Carbon::now(),
                    "features" => json_encode([
                        "300 Projects",
                        "Up to 500 subscribers",
                        "Advanced analytics",
                        "24-hour support",
                        "Marketing advisor"
                    ])
                ]
            ]);
        }
    }
}
