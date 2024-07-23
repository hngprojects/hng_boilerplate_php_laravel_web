<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     */
    public function test_registration_returns_jwt_token()
    {
        $registrationData = [
            'name' => 'Test User',
            'email' => 'testuser@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson('/api/v1/auth/register', $registrationData);
        // Check the status code
        $response->assertStatus(201);

        // Check the response structure
        $response->assertJsonStructure([
            'status',
            'message',
            'status_code',
            'data' => [
                'accessToken',
                'user' => [
                    'name',
                    'email',
                    'id',
                    'updated_at',
                    'created_at',
                ]
            ]
        ]);

        // Optionally, decode and verify the token
        $token = $response->json('data.accessToken');
        $this->assertNotEmpty($token);
    }

    public function test_fails_if_email_is_not_passed()
    {
        $registrationData = [
            'name' => 'Test User',
            'email' => '',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson('/api/v1/auth/register', $registrationData);
        // Check the status code
        $response->assertStatus(422);
        $response->assertJson([
            'status' => "Forbidden",
            'message' => [
                'email' => [
                    'The email field is required.'
                ]
            ],
            'status_code' => 422,
        ]);
    }
}
