<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RetrieveAllSubscriptionPlansTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_retrieves_billing_plans_successfully()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');
        $response = $this->getJson('/api/v1/plans');

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
}
