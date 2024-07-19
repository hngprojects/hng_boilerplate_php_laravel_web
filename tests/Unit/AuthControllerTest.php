<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Set up code here, like seeding the database
    }

    public function test_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('examplePass'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'user@example.com',
            'password' => 'examplePass',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'token',
                 ]);

        $token = $response->json('token');
        $this->assertNotEmpty($token);
        $this->assertTrue(JWTAuth::setToken($token)->check());
    }

    public function test_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('examplePass'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'user@example.com',
            'password' => 'wrongPass',
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'error' => 'Authentication failed',
                     'message' => 'Invalid email or password.',
                 ]);
    }

    public function test_login_with_missing_parameters()
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'user@example.com',
        ]);

        $response->assertStatus(400)
                 ->assertJson([
                     'error' => 'Invalid request parameters',
                     'message' => 'Email and password are required.',
                 ]);
    }

    public function test_database_connection()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'user@example.com',
        ]);
    }
}
