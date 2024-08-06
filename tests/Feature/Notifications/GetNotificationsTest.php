<?php

namespace Tests\Feature\Notifications;

use App\Models\Notification;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class GetNotificationsTest extends TestCase
{
    use RefreshDatabase;

    private function getAuthenticatedUser()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);
        // $token = JWTAuth::attempt(['email' => $user->email, 'password' => 'password']);

        return [$user, $token];
    }

    /**
     * Test get all notifications as an authenticated user.
     *
     * @return void
     */
    public function test_get_all_unread_users_notifications()
    {
        // Login
        [$user, $token] = $this->getAuthenticatedUser();

        // Create a notificatin for the user
        UserNotification::factory()->create([
            'user_id' => $user->id,
        ]);

        // Authenticate the user and send a GET
        $response = $this->getJson('/api/v1/notifications?is_read=false', [
            'Authorization' => 'Bearer ' . $token,
        ]);

        // Assert the response status and structure
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'status_code',
                'data' => [
                    'total_notification_count',
                    'total_unread_notification_count',
                    'notifications' => [
                        // 'id',
                        // 'is_read',
                        // 'message',
                        // 'created_at',
                    ]
                ],
            ]);
    }

    /**
     * Test get all notifications as an authenticated user.
     *
     * @return void
     */
    public function test_get_all_users_notifications()
    {
        // Login
        [$user, $token] = $this->getAuthenticatedUser();

        // Create a notificatin for the user
        UserNotification::factory()->create([
            'user_id' => $user->id,
        ]);

        // Authenticate the user and send a GET
        $response = $this->getJson('/api/v1/notifications/', [
            'Authorization' => 'Bearer ' . $token,
        ]);

        // Assert the response status and structure
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'status_code',
                'data' => [
                    'total_notification_count',
                    'total_unread_notification_count',
                    'notifications' => [
                        // 'id',
                        // 'is_read',
                        // 'message',
                        // 'created_at',
                    ]
                ],
            ]);
    }


    /**
     * Test updating a testimonial as an unauthenticated user.
     *
     * @return void
     */
    public function test_cannot_get_notification_unauthenticated()
    {

        // Send a GET request without authentication
        $response = $this->getJson('/api/v1/notifications/');

        // Assert the response status
        $response->assertStatus(401);
    }
}
