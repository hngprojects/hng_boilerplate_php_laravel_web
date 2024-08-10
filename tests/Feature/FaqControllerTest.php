<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\User;
use Database\Seeders\FaqSeeder;
use Illuminate\Http\Response;
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

    public function test_index_returns_faqs()
    {
        $this->seed(FaqSeeder::class);

        $response = $this->getJson('/api/v1/faqs');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => ['id', 'question', 'answer', 'category', 'createdBy', 'createdAt', 'updatedAt']
                ]
                ]);
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
    }

    public function test_if_admin_can_delete_faq()
    {
        
        // Create a FAQ
        $faq = Faq::create([
            'question' => 'What is the safe policy?',
            'answer' => 'Our safe policy allows returns within 30 days of purchase.',
            'category' => 'Policies'
        ]);

        $response = $this->withHeaders(['Authorization' => "Bearer $this->adminToken"])
            ->deleteJson("/api/v1/faqs/{$faq->id}");

        // Assert that the FAQ is deleted
        $response->assertStatus(Response::HTTP_OK)
                 ->assertJson([
                     'status_code' => 200,
                     'message' => 'FAQ successfully deleted.',
                 ]);

        $this->assertDatabaseMissing('faqs', ['id' => $faq->id]);
    }

    public function test_delete_non_existent_faq()
    {
        $response = $this->withHeaders(['Authorization' => "Bearer $this->adminToken"])
            ->deleteJson("/api/v1/faqs/9999");

        // Assert that the FAQ is deleted
        $response->assertStatus(400)
                ->assertJson([
                    'status_code' => 400,
                    'message' => 'Bad Request.',
                ]);

    }

    

    

    
}
