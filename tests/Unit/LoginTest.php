<?php

namespace Tests\Unit;

use Illuminate\Http\Response;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Config::set('auth.throttle.max_attempts', 3);
        Config::set('auth.throttle.decay_minutes', 60);
    }

    public function test_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@gmail.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@gmail.com',
            'password' => 'password123',
        ]);

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

    public function test_login_with_invalid_credentials()
    {
        User::factory()->create([
            'email' => 'test@gmail.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@gmail.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid credentials',
            ]);
    }

    public function test_login_with_missing_fields()
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@gmail.com',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_login_attempt_limit()
    {
        User::factory()->create([
            'email' => 'test@gmail.com',
            'password' => Hash::make('password123'),
        ]);

        for ($i = 0; $i < 3; $i++) {
            $this->postJson('/api/v1/auth/login', [
                'email' => 'test@gmail.com',
                'password' => 'wrongpassword',
            ]);
        }

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@gmail.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Too Many login attempts. Please try again in one hour',
                'error' => 'too_many_attempts',
                'status_code' => 403
            ]);
    }

    public function test_successful_login_after_rate_limit_expires()
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        for ($i = 0; $i < 3; $i++) {
            $this->postJson('/api/v1/auth/login', [
                'email' => 'test@example.com',
                'password' => 'wrongpassword',
            ]);
        }

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
