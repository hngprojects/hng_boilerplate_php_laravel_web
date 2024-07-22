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
}
