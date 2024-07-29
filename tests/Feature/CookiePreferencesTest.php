<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class CookiePreferencesTest extends TestCase
{
    use RefreshDatabase;

    private $accessToken;

    /** @test */
    public function it_registers_a_user()
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'first_name' => 'precious',
            'last_name' => 'test',
            'email' => 'precious@test.com',
            'password' => '120oklsQQMNu)',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'first_name',
                        'last_name',
                        'email',
                        'role',
                        'signup_type',
                        'is_active',
                        'is_verified',
                        'created_at',
                        'updated_at'
                    ],
                    'access_token',
                    'refresh_token'
                ]
            ]);
    }

    /** @test */
    public function it_logs_in_a_user()
    {
        // First, register the user
        $this->postJson('/api/v1/auth/register', [
            'first_name' => 'precious',
            'last_name' => 'test',
            'email' => 'precious@test.com',
            'password' => '120oklsQQMNu)',
        ]);

        // Now, log in the user
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'precious@test.com',
            'password' => '120oklsQQMNu)',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'first_name',
                        'last_name',
                        'email',
                        'role',
                        'signup_type',
                        'is_active',
                        'is_verified',
                        'created_at',
                        'updated_at'
                    ],
                    'access_token',
                    'refresh_token'
                ]
            ]);

        // Store the access token for use in subsequent tests
        $this->accessToken = $response->json('data.access_token');
    }

    /** @test */
    public function it_updates_cookie_preferences()
    {
        // Ensure user is registered and logged in to get access token
        if (!$this->accessToken) {
            $this->it_logs_in_a_user();
        }

        // Now, update cookie preferences
        $response = $this->postJson('/api/v1/cookies/preferences', [
            'user_id' => '9ca3311f-c764-451e-a53d-fba8c3d3444f',
            'preferences' => [
                'analytics_cookies' => true,
                'marketing_cookies' => false,
                'functional_cookies' => true,
            ]
        ], [
            'Authorization' => 'Bearer ' . $this->accessToken
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status_code' => 200,
                'success' => true,
                'message' => 'Cookie preferences updated successfully.',
                'data' => [
                    'user_id' => '9ca3311f-c764-451e-a53d-fba8c3d3444f',
                    'preferences' => [
                        'analytics_cookies' => true,
                        'marketing_cookies' => false,
                        'functional_cookies' => true,
                    ]
                ]
            ]);
    }
}
