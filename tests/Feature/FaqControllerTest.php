<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\User;
use Database\Seeders\FaqSeeder;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class FaqControllerTest extends TestCase
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

    public function test_index_returns_paginated_faqs()
    {
        $this->seed(FaqSeeder::class);

        $response = $this->withHeaders(['Authorization' => "Bearer $this->adminToken"])
            ->getJson('/api/v1/faqs?page=1&size=5');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => ['id', 'question', 'answer']
                ],
                'pagination' => ['current_page', 'total_pages', 'page_size', 'total_items']
            ])
            ->assertJsonCount(5, 'data');
    }

    public function test_index_returns_faqs_without_pagination()
    {
        $response = $this->withHeaders(['Authorization' => "Bearer $this->adminToken"])
            ->getJson('/api/v1/faqs');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => ['id', 'question', 'answer']
                ],
            ]);
    }


    public function test_if_it_fails_for_unathorised_access_to_faqs()
    {

        $response = $this->getJson('/api/v1/faqs');
        $response->assertStatus(401);
    }

    public function test_it_deletes_a_faq_successfully()
    {
        // Arrange: Seed the database and create a FAQ instance
        $this->seed(FaqSeeder::class);
        $faq = Faq::first();

        // Act: Send DELETE request to delete the FAQ
        $response = $this->withHeaders(['Authorization' => "Bearer $this->adminToken"])
            ->deleteJson("/api/v1/faqs/{$faq->id}");

        // Assert: Verify the response
        $response->assertStatus(200)
            ->assertJson([
                'code' => 200,
                'description' => 'The FAQ has been successfully deleted.',
                'links' => []
            ]);

        // Assert: Verify the FAQ has been deleted from the database
        $this->assertDatabaseMissing('faqs', ['id' => $faq->id]);
    }

    public function test_it_returns_bad_request_when_faq_not_found()
    {
        // Generate a UUID that does not exist in the database
    $invalidUuid = (string) Str::uuid();

        // Act: Send DELETE request to delete a non-existent FAQ
        $response = $this->withHeaders(['Authorization' => "Bearer $this->adminToken"])
            ->deleteJson('/api/v1/faqs/' . $invalidUuid);

        // Assert: Verify the response
        $response->assertStatus(400)
            ->assertJson([
                'code' => 400,
                'description' => 'Bad Request.',
                'links' => []
            ]);
    }
}
