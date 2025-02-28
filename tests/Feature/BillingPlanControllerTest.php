<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class BillingPlanControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_a_billing_plan_successfully()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $saved = SubscriptionPlan::where('name', '=', 'Discounted Plan')->first();

        if($saved) $saved->delete();

        $data = [
            'name' => 'Discounted Plan',
            'duration' => 'Yearly',
            'price' => 20000,
            'description' => 'A discounted plan description',
        ];

        $response = $this->postJson("/api/v1/billing-plans", $data);

        // Assert: Check the response status and structure
        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'name',
                         'duration',
                         'price',
                         'description',
                         'created_at',
                         'updated_at',
                     ],
                     'message',
                     'status_code',
                     'status'
                 ]);

        $this->assertDatabaseHas('subscription_plans',  [
            'name' => 'Discounted Plan',
            'duration' => 'Yearly',
            'price' => 20000,
            'description' => 'A discounted plan description',
        ]);


        $res = $this->postJson("/api/v1/billing-plans", $data);

        // Assert: Check the response status and structure
        $res->assertStatus(403)
                 ->assertJsonStructure([
                     'data' => [],
                     'message',
                     'status_code',
                     'status'
                 ]);

    }

    /** @test */
    public function it_updates_a_billing_plan_successfully()
    {
        // Arrange: Create a subscription plan in the database
        $plan = SubscriptionPlan::factory()->create([
            'name' => 'Basic Plan',
            'duration' => 'Monthly',
            'price' => 10000, // stored as kobo
            'description' => 'A basic plan'
        ]);

        // Define the update data
        $updateData = [
            'name' => 'Updated Plan',
            'frequency' => 'Yearly',
            // 'is_active' => false,
            'amount' => 20000, // updated price in kobo
            'description' => 'An updated plan description',
        ];

        // Act: Send a PUT request to update the billing plan
        $response = $this->putJson("/api/v1/billing-plans/{$plan->id}", $updateData);

        // Assert: Check the response status and structure
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'name',
                         'frequency',
                         'amount',
                         'description',
                         'created_at',
                         'updated_at',
                     ],
                     'message',
                     'status_code',
                 ]);

        // Assert: Check that the database has been updated
        $this->assertDatabaseHas('subscription_plans', [
            'id' => $plan->id,
            'name' => 'Updated Plan',
            'duration' => 'Yearly',
            'price' => 20000,
            'description' => 'An updated plan description',
        ]);
    }
}
