<?php

namespace Tests\Feature\User;

use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class UserSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_route_is_valid_and_jwt_protected()
    {
        $user = User::factory()->create();
//        $this->actingAs($user);
        $userSub = UserSubscription::factory()->create([
            'user_id' => $user->id
        ]);
        $response = $this->post("/api/v1/users/plans/{$userSub->id}/cancel", [
            'cancellation_reason' => 'i dont like this service anymore'
        ], ['accept' => 'application/json']);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_plan_can_be_cancelled_successfully()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $userSub = UserSubscription::factory()->create([
            'user_id' => $user->id
        ]);
        $response = $this->post("/api/v1/users/plans/{$userSub->id}/cancel", [
            'cancellation_reason' => 'i dont like this service anymore'
        ], ['accept' => 'application/json']);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(['message' => 'subscription plan cancelled successfully']);
    }

    public function test_plan_cannot_be_cancelled_repeatedly()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $userSub = UserSubscription::factory()->create([
            'user_id' => $user->id
        ]);
        $this->post("/api/v1/users/plans/{$userSub->id}/cancel", [
            'cancellation_reason' => 'i dont like this service anymore'
        ], ['accept' => 'application/json']);
        $response = $this->post("/api/v1/users/plans/{$userSub->id}/cancel", [
            'cancellation_reason' => 'i dont like this service anymore'
        ], ['accept' => 'application/json']);
        $response->assertStatus(Response::HTTP_CONFLICT);
        $response->assertJson(['message' => 'subscription plan has already been cancelled']);
    }
}
