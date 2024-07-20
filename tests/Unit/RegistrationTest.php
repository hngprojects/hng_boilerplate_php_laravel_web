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
            'email' => 'testuser@example.com',
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
            'status-code',
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
}
