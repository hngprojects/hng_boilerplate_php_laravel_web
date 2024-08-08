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
                        'id',
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



    public function test_if_admin_can_create_squeeze_page()
    {
        $payload = [
            'title' => 'Email Mastery',
            'slug' => 'email-mastery',
            'status' => 'online',
            'activate' => true,
            'headline' => 'Master the Art of Email Marketing',
            'sub_headline' => 'Unlock the Secrets to Email Campaign Success',
            'hero_image' => 'email_marketing_hero.jpg',
            'content' => 'This is a comprehensive guide to mastering email marketing...',
        ];

        $response = $this->withHeaders(['Authorization' => "Bearer $this->adminToken"])
            ->postJson('/api/v1/squeeze-pages', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status_code',
                'message',
                'data' => ['id', 'title', 'slug', 'created_at', 'status', 'activate']
            ]);

        $this->assertDatabaseHas('squeeze_pages', $payload);
    }

    public function test_if_create_squeeze_page_missing_field_fails()
    {
        $payload = [
            'slug' => 'email-mastery',
            'status' => 'online',
            'activate' => true,
            'headline' => 'Master the Art of Email Marketing',
            'sub_headline' => 'Unlock the Secrets to Email Campaign Success',
            'hero_image' => 'email_marketing_hero.jpg',
            'content' => 'This is a comprehensive guide to mastering email marketing...',
        ];

        $response = $this->withHeaders(['Authorization' => "Bearer $this->adminToken"])
            ->postJson('/api/v1/squeeze-pages', $payload);

        $response->assertStatus(400)
            ->assertJsonStructure([
                'status_code',
                'errors' => [
                    '*' => [
                        'field',
                        'message'
                    ],
                ]
            ]);
    }
}
