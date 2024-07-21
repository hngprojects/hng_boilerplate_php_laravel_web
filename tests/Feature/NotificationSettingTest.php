<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class NotificationSettingTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanUpdateNotificationSettings()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->putJson('/api/v1/notification-settings/' . $user->id, [
            'email_notifications' => false,
            'sms_notifications' => true,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Notification settings updated successfully',
        ]);
    }
}
