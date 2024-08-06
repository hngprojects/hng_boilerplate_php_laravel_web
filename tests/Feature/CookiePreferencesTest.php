<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CookiePreferencesTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cookie_preferences_workflow()
    {
        // Step 1: Register the user
        $registerResponse = $this->postJson('/api/v1/auth/register', [
            'first_name' => 'precious',
            'last_name' => 'test',
            'email' => 'precious@test.com',
            'password' => '120oklsQQMNu)'
        ]);

        $registerResponse->assertStatus(201);

        // Step 2: Log in the user
        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'email' => 'precious@test.com',
            'password' => '120oklsQQMNu)'
        ]);

        $loginResponse->assertStatus(200)
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

        $userId = $loginResponse->json('data.user.id');
        $accessToken = $loginResponse->json('data.access_token');

        // Step 3: Update cookie preferences
        $cookiePreferencesResponse = $this->withHeaders([
            'Authorization' => "Bearer $accessToken"
        ])->postJson('/api/v1/cookies/preferences', [
            'user_id' => $userId,
            'preferences' => [
                'analytics_cookies' => true,
                'marketing_cookies' => false,
                'functional_cookies' => true
            ]
        ]);

        $cookiePreferencesResponse->assertStatus(200)
            ->assertJson([
                'status_code' => 200,
                'success' => true,
                'message' => 'Cookie preferences updated successfully.',
                'data' => [
                    'user_id' => $userId,
                    'preferences' => [
                        'analytics_cookies' => true,
                        'marketing_cookies' => false,
                        'functional_cookies' => true
                    ]
                ]
            ]);
        // Step 4: Retrieve cookie preferences
        $retrievePreferencesResponse = $this->withHeaders([
            'Authorization' => "Bearer $accessToken"
        ])->getJson('/api/v1/cookies/preferences?user_id=' . $userId);

        $retrievePreferencesResponse->assertStatus(200)
            ->assertJson([
                'status_code' => 200,
                'success' => true,
                'data' => [
                    'user_id' => $userId,
                    'preferences' => [
                        'analytics_cookies' => true,
                        'marketing_cookies' => false,
                        'functional_cookies' => true
                    ]
                ]
            ]);
    }
}
