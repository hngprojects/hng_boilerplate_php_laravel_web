<?php

namespace Tests\Feature\Notifications;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class CreateNotificationTest extends TestCase
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
     * Test creating a new notification as an authenticated user.
     *
     * @return void
     */
    public function test_should_create_new_notification()
    {
        // Login
        [$user, $token] = $this->getAuthenticatedUser();

        // Define the data
        $data = [
            'message' => 'This is an new notification.',
        ];

        // Authenticate the user and send a POST request
        $response = $this->postJson('/api/v1/notifications', $data, [
            'Authorization' => 'Bearer ' . $token,
        ]);

        // Assert the response status and structure
        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'status_code',
                'data' => [
                    'id',
                    // 'user_id',
                    'message',
                    'created_at',
                ],
            ]);
    }

    /**
     * Test creating a notification with invalid data.
     *
     * @return void
     */
    public function test_should_not_create_notification_with_invalid_missing_data()
    {
        // Create a user
        [$user, $token] = $this->getAuthenticatedUser();

        // Send a POST request with missing data (missing content)
        $response1 = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->postJson('/api/v1/notifications'); // No data

        // Send a POST request with invalid data
        $data = [
            'content' => ''
        ];
        $response2 = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->postJson('/api/v1/notifications', $data); // No data

        // Assert the response status and structure
        $response1->assertStatus(422)
            ->assertJsonStructure([
                'errors' => [
                    [
                        'field',
                        'message',
                    ],
                ],
            ]);
        // Assert the response status and structure
        $response2->assertStatus(422)
            ->assertJsonStructure([
                'errors' => [
                    [
                        'field',
                        'message',
                    ],
                ],
            ]);
    }

    /**
     * Test creating a notification as an unauthenticated user.
     *
     * @return void
     */
    public function test_should_not_create_notification_unauthenticated()
    {

        // Define the data
        $data = [
            'message' => 'This is a new testimonial.',
        ];

        // Send a POST request without authentication
        $response = $this->postJson('/api/v1/notifications', $data);

        // Assert the response status
        $response->assertStatus(401);
    }
}
