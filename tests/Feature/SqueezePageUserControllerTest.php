<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\SqueezePageUser;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class SqueezePageUserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $adminToken;

    public function setUp(): void
    {
        parent::setUp();
        
        $this->adminUser = User::factory()->create(['role' => 'admin']);
        $this->regularUser = User::factory()->create(['role' => 'user']);
        $this->adminToken = JWTAuth::fromUser($this->adminUser);
        $this->regularToken = JWTAuth::fromUser($this->regularUser);
    }

    /** @test */
    public function it_can_store_user_details()
    {
        $data = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'john.doe@example.com',
            'title' => 'Mr.',
        ];

        $response = $this->postJson('/api/v1/squeeze-user', $data);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'User details successfully added to squeeze_pages_user.',
                'data' => [
                    'firstname' => 'John',
                    'lastname' => 'Doe',
                    'email' => 'john.doe@example.com',
                    'title' => 'Mr.',
                ],
                'status_code' => 201,
            ]);

        $this->assertDatabaseHas('squeeze_pages_user', $data);
    }

    /** @test */
    public function it_can_fetch_user_details_with_pagination()
    {
        SqueezePageUser::factory()->count(15)->create();

        $response = $this->withHeaders(['Authorization' => "Bearer $this->adminToken"])
                ->getJson('/api/v1/squeeze-pages-users?page=1&limit=10');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => ['firstname', 'lastname', 'email', 'title', 'created_at'],
                ],
                'pagination' => [
                    'totalItems',
                    'totalPages',
                    'currentPage',
                ],
                'status_code',
            ]);

        
            $response = $this->withHeaders(['Authorization' => "Bearer $this->adminToken"])
            ->getJson('/api/v1/squeeze-pages-users?page=2&limit=10');

    }
}
