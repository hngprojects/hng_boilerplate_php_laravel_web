<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\User;
use Database\Seeders\FaqSeeder;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_if_admin_can_create_faq()
    {
        $payload = [
            'question' => 'What is the return policy?',
            'answer' => 'Our return policy allows returns within 30 days of purchase.',
            'category' => 'Policies'
        ];

        $response = $this->withHeaders(['Authorization' => "Bearer $this->adminToken"])
            ->postJson('/api/v1/faqs', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'question',
                    'answer',
                    'category',
                    'createdAt',
                    'updatedAt',
                    'createdBy'
                ]
            ]);

        $this->assertDatabaseHas('faqs', $payload);
    }

    public function test_if_create_faq_missing_field_fails()
    {
        $payload = [
            'answer' => 'Our return policy allows returns within 30 days of purchase.',
            'category' => 'Policies'
        ];

        $response = $this->withHeaders(['Authorization' => "Bearer $this->adminToken"])
            ->postJson('/api/v1/faqs', $payload);

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


    public function test_if_admin_can_edit_faq()
    {

        $faq = Faq::create([
            'question' => 'What is the safe policy?',
            'answer' => 'Our safe policy allows returns within 30 days of purchase.',
            'category' => 'Policies'
        ]);

        $payload = [
            'question' => 'What is the disposal policy?',
            'answer' => 'Our disposal policy allows returns within 30 days of purchase.',
            'category' => 'Policies'
        ];

        $response = $this->withHeaders(['Authorization' => "Bearer $this->adminToken"])
            ->putJson("/api/v1/faqs/{$faq->id}", $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'question',
                    'answer',
                    'category',
                    'createdAt',
                    'updatedAt',
                    'createdBy'
                ]
            ]);

        $this->assertDatabaseHas('faqs', [
            'id' => $faq->id,
            'question' => 'What is the disposal policy?',
            'answer' => 'Our disposal policy allows returns within 30 days of purchase.',
            'category' => 'Policies'
        ]);
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
