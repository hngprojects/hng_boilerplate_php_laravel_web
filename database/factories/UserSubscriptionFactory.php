<?php

namespace Database\Factories;

use App\Models\SubscriptionPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Organisation;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserSubscription>
 */
class UserSubscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $plan = SubscriptionPlan::factory()->create();
        return [
            'user_id' => User::factory(),
            'subscription_plan_id' => $plan,
            'cancellation_reason' => $this->faker->text(),
            'expires_at' => function () use ($plan) {
                $now = Carbon::now();
                if ($plan->duration === 'monthly') {
                    return $now->addMonths(1);
                } else {
                    return $now->addYear();
                }
            },
            'org_id' => Organisation::inRandomOrder()->first()->id ?? Organisation::factory(),
        ];
    }
}
