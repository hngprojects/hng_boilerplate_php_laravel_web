<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    private $accessToken;

    protected function setUp(): void
    {
        parent::setUp();

        // Register a new user
        $response = $this->postJson('/api/v1/auth/register', [
            'first_name' => 'Precious',
            'last_name' => 'Test',
            'email' => 'precious@test.com',
            'password' => '120oklsQQMNu)',
        ]);

        $response->assertStatus(201);

        $this->accessToken = $response->json('access_token');
    }



    public function testUserLogin()
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'precious@test.com',
            'password' => '120oklsQQMNu)',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'data' => ['user' => ['id', 'first_name', 'last_name', 'email', 'role', 'avatar_url']]
        ]);
    }
}
