<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test login with valid credentials.
     *
     * @return void
     */
    public function test_login_with_valid_credentials()
    {
        // Create a test user
        $user = User::create([
            'name' => 'Tiamiyu Rasheed',
            'email' => 'test@gmail.com',
            'password' => Hash::make('password123'),
        ]);

        // Attempt to login
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@gmail.com',
            'password' => 'password123',
        ]);

        // Assert successful login
        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'email',
                        'role',
                        'signup_type',
                        'is_active',
                        'is_verified',
                        'created_at',
                        'updated_at',
                    ],
                    'access_token',
                    'refresh_token',
                ],
            ]);
    }

    /**
     * Test login with invalid credentials.
     *
     * @return void
     */
    public function test_login_with_invalid_credentials()
    {
        // Create a test user
        $user = User::create([
            'name' => 'Tiamiyu Rasheed',
            'email' => 'test@gmail.com',
            'password' => Hash::make('password123'),
        ]);

        // Attempt to login with invalid credentials
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@gmail.com',
            'password' => 'wrongpassword',
        ]);

        // Assert unsuccessful login
        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid credentials',
            ]);
    }

    /**
     * Test login with missing fields.
     *
     * @return void
     */
    public function test_login_with_missing_fields()
    {
        // Attempt to login with missing fields
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@gmail.com',
        ]);

        // Assert validation error
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test login attempt limit.
     *
     * @return void
     */
    public function test_login_attempt_limit()
    {
        // Create a test user
        $user = User::create([
            'name' => 'Tiamiyu Rasheed',
            'email' => 'test@gmail.com',
            'password' => Hash::make('password123'),
        ]);

        // Attempt to login with wrong password 3 times
        for ($i = 0; $i < 3; $i++) {
            $response = $this->postJson('/api/v1/auth/login', [
                'email' => 'test@gmail.com',
                'password' => 'wrongpassword',
            ]);
            $response->assertStatus(401);
        }

        // The 4th attempt should be blocked
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@gmail.com',
            'password' => 'wrongpasswords',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Too Many login attempts. Please try again in one hour',
                'error' => 'too_many_attempts',
                'status_code' => 403
            ]);
    }

    /**
     * Test successful login after rate limit expires.
     *
     * @return void
     */
    public function test_successful_login_after_rate_limit_expires()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        for ($i = 0; $i < 3; $i++) {
            $this->postJson('/api/v1/auth/login', [
                'email' => 'test@example.com',
                'password' => 'wrongpassword',
            ]);
        }

        // Simulate waiting for an hour
        $this->travel(61)->minutes();

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'user',
                    'access_token',
                    'refresh_token',
                ],
            ]);
    }
}
