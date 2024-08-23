<?php

namespace Tests\Feature\Auth;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        Profile::factory()->create([
            'user_id' => $user->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status_code',
                'message',
                'access_token',
                'data' => [
                    'user' => [
                        'id',
                        'first_name',
                        'last_name',
                        'email',
                        'avatar_url',
                        'is_superadmin',
                    ],
                    'organisations',
                ],
            ])
            ->assertJson([
                'status_code' => 200,
                'message' => 'Login successful',
            ]);
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'status_code' => 401,
                'message' => 'Invalid credentials',
            ]);
    }
}
