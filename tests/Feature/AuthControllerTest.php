<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('examplePass')
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'user@example.com',
            'password' => 'examplePass'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token']);
    }

    public function test_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('examplePass')
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'user@example.com',
            'password' => 'wrongPass'
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'error' => 'Authentication failed',
                'message' => 'Invalid email or password.'
            ]);
    }

    public function test_login_with_missing_parameters()
    {
        $response = $this->postJson('/api/v1/auth/login', []);

        $response->assertStatus(400)
            ->assertJson([
                'error' => 'Invalid request parameters',
                'message' => 'Email and password are required.'
            ]);
    }
}
