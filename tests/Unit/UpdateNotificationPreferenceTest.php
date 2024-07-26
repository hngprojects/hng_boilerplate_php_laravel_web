<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\NotificationPreference;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateNotificationPreferenceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_requires_authentication()
    {
        $response = $this->patchJson('/api/v1/notification-settings/9c997571-98e5-48cb-8118-37dc384ca323');
        $response->assertStatus(404);
    }

    /** @test */
    public function it_allows_owner_to_update_settings()
    {
        $user = User::factory()->create();
        $notificationPreference = NotificationPreference::factory()->create(['user_id' => $user->id]);

        $data = [
            'email_notifications' => true,
            'push_notifications' => true,
            'sms_notifications' => false,
        ];

        $response = $this->actingAs($user, 'api')
            ->patchJson("/api/v1/notification-settings/{$user->id}", $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('notification_preferences', $data + ['user_id' => $user->id]);
    }

    /** @test */
    public function it_disallows_non_owner_to_update_settings()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        NotificationPreference::factory()->create(['user_id' => $user->id]);

        $data = [
            'email_notifications' => true,
        ];

        $response = $this->actingAs($otherUser, 'api')
            ->patchJson("/api/v1/notification-settings/{$user->id}", $data);

        $response->assertStatus(401);
    }

    /** @test */
    public function it_validates_request_data()
    {
        $user = User::factory()->create();
        NotificationPreference::factory()->create(['user_id' => $user->id]);

        $data = [
            'email_notifications' => 'invalid_value',
        ];

        $response = $this->actingAs($user, 'api')
            ->patchJson("/api/v1/notification-settings/{$user->id}", $data);

        $response->assertStatus(400);
    }
}