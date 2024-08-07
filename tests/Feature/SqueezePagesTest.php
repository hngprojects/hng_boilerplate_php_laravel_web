<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class SqueezePagesTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $adminToken;

    public function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create(['role' => 'admin']);
        $this->adminToken = JWTAuth::fromUser($this->adminUser);
    }


    public function test_it_retrieves_squeeze_pages_successfully()
    {
        $response = $this->withHeaders(['Authorization' => "Bearer $this->adminToken"])
            ->getJson('/api/v1/squeeze-pages');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    '*' => [
                        'uuid',
                        'title',
                        'slug',
                        'created_at',
                        'status',
                        'activate',
                    ]
                ]
            ]);
    }

    public function test_if_it_fails_for_unathorised_access_to_squeeze_pages()
    {

        $response = $this->getJson('/api/v1/squeeze-pages');
        $response->assertStatus(401);
    }
}
