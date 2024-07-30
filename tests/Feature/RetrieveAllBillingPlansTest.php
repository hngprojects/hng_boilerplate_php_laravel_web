<?php

namespace Tests\Feature;

use App\Models\BillingPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class RetrieveAllBillingPlansTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_retrieves_billing_plans_successfully()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

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

    // public function test_it_returns_500_error_on_exception()
    // {
    //     $this->withoutExceptionHandling();

    //     $user = User::factory()->create();
    //     $this->actingAs($user, 'api');

    //     $this->mock(BillingPlan::class, function ($mock) {
    //         $mock->shouldReceive('all')->andThrow(new \Exception('Internal server error'));
    //     });

    //     $response = $this->getJson('/api/v1/billing-plans');

    //     $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
    //     ->assertJson([
    //         'message' => 'Internal server error',
    //         'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
    //     ]);
    // }
}
