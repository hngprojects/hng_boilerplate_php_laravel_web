<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

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
        $response = $this->get('/api/v1/help-center/topics/search?title=Test');

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
}
