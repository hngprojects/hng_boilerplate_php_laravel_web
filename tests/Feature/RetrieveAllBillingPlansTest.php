<?php

namespace Tests\Feature;

use App\Models\BillingPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RetrieveAllBillingPlansTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_retrieves_billing_plans_successfully()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson('/api/v1/billing-plans');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'price'
                    ]
                ]
            ]);
    }

    public function test_it_fails_to_retrieve_billing_plans_for_unauthorized_user()
    {
        $response = $this->getJson('/api/v1/billing-plans');

        $response->assertStatus(401);
    }

    public function test_it_returns_500_error_on_exception()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->mock(BillingPlan::class, function ($mock) {
            $mock->shouldReceive('all')->andThrow(new \Exception('Server Error'));
        });

        $response = $this->getJson('/api/v1/billing-plans');

        $response->assertStatus(500);
    }
}
