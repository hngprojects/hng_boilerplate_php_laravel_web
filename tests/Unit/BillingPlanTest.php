<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\BillingPlan;

class BillingPlanTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_retrieve_a_billing_plan_by_id()
    {
        // Create a billing plan
        $billingPlan = BillingPlan::factory()->create();

        // Perform GET request to the endpoint
        $response = $this->getJson("/api/v1/billing-plans/{$billingPlan->id}");

        // Assert the response
        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 200,
                     'message' => 'Pricing plan retrieved successfully',
                     'data' => [
                         'id' => $billingPlan->id,
                         'name' => $billingPlan->name,
                         'price' => $billingPlan->price,
                     ],
                 ]);
    }

    
    
}
