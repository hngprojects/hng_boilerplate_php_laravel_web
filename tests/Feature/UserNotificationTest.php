<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserNotification;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class UserNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_clear_single_notification()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $userNotification = UserNotification::factory()->create([
            'user_id' => $user->id,
        ]);
        $response = $this->patch("/api/v1/notifications/{$userNotification->id}", [
            'is_read' => true,
        ]);
        $response->assertSuccessful();
        $this->assertDatabaseHas('user_notifications', [
            'id' => $userNotification->id,
            'status' => 'read',
        ]);
    }

    public function test_user_can_clear_all_notifications()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        UserNotification::factory()->count(2)->create([
            'user_id' => $user->id,
            'status' => 'unread',
        ]);
        $response = $this->delete("/api/v1/notifications");
        $response->assertSuccessful();
        $response->assertJsonCount(2, 'data');
    }

    public function test_validation_trap_is_read_not_passed()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $userNotification = UserNotification::factory()->create([
            'user_id' => $user->id,
        ]);
        $response = $this->patch("/api/v1/notifications/{$userNotification->id}", [], [
            'accept' => 'application/json',
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['is_read']);
    }
}
