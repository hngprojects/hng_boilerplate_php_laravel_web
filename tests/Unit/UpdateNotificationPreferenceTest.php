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

    /** @test */
    public function it_creates_settings_if_none_exist()
    {
        $user = User::factory()->create();

        $data = [
            'mobile_push_notifications' => true,
            'email_notification_activity_in_workspace' => true,
            'email_notification_always_send_email_notifications' => true,
            'email_notification_email_digest' => true,
            'email_notification_announcement_and_update_emails' => true,
            'slack_notifications_activity_on_your_workspace' => true,
            'slack_notifications_always_send_email_notifications' => true,
            'slack_notifications_announcement_and_update_emails' => true,
        ];

        $response = $this->actingAs($user, 'api')
            ->patchJson('/api/v1/settings/notification-settings', $data);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Notification preferences updated successfully',
                'status_code' => 200,
            ]);

        $this->assertDatabaseHas('notification_settings', $data + ['user_id' => $user->id]);
    }
}
