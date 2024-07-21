<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_search_articles()
    {
        // Create some test articles
        Article::factory()->create(['title' => 'Test Article 1']);
        Article::factory()->create(['title' => 'Test Article 2']);
        Article::factory()->create(['title' => 'Different Article']);

        // Perform the search
        $response = $this->get('/api/v1/topics/search?title=Test');

        // Assert the response
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'status_code',
                'topics' => [
                    '*' => [
                        'article_id',
                        'author',
                        'title',
                        'content'
                    ]
                ]
            ])
            ->assertJsonCount(2, 'topics');
    }

    public function test_search_without_title_parameter()
    {
        $response = $this->get('/api/v1/topics/search');

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Title query parameter is required',
                'status_code' => 400
            ]);
    }

    public function test_search_with_no_results()
    {
        $response = $this->get('/api/v1/topics/search?title=NonexistentArticle');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'No article matches the title search param.',
                'status_code' => 404
            ]);
    }
}
