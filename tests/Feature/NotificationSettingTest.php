<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationSettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_notification_settings()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->putJson('/api/notification-settings', [
            'email_notifications' => false,
            'sms_notifications' => true,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Notification settings updated successfully',
        ]);

        $this->assertDatabaseHas('notification_settings', [
            'user_id' => $user->id,
            'email_notifications' => false,
            'sms_notifications' => true,
        ]);
    }
}
