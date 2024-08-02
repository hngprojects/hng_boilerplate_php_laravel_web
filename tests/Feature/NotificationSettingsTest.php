<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\NotificationSettings;

class NotificationSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_notification_settings()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');
        $response = $this->getJson('/api/v1/notification-settings');
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'message',
                     'status_code',
                     'data' => [
                         'email_notification_activity_in_workspace',
                         'email_notification_always_send_email_notifications',
                         'email_notification_email_digest',
                         'email_notification_announcement_and_update_emails',
                         'slack_notifications_activity_on_your_workspace',
                         'slack_notifications_always_send_email_notifications',
                         'slack_notifications_announcement_and_update_emails'
                     ]
                 ]);
    }

    public function test_update_notification_settings()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');
        $response = $this->patchJson('/api/v1/notification-settings', [
            'email_notification_activity_in_workspace' => true,
        ]);
        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'Notification preferences updated successfully',
                     'status_code' => 200,
                     'data' => [
                         'email_notification_activity_in_workspace' => true,
                     ]
                 ]);
    }
}
