<?php

namespace Tests\Feature;

use App\Models\Blog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_search_blogs()
    {
        Blog::factory()->count(20)->create();

        $response = $this->getJson('/api/v1/blogs/search?content=test&page=1&page_size=10');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'current_page',
                'total_pages',
                'total_results',
                'blogs' => [
                    '*' => [
                        'id',
                        'title',
                        'content',
                        'author',
                        'created_date',
                        'tags',
                    ]
                ],
                'meta' => [
                    'has_next',
                    'total',
                    'next_page',
                    'prev_page',
                ]
            ]);
    }

    public function test_search_with_invalid_params()
    {
        $response = $this->getJson('/api/v1/blogs/search?page=-1');

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'error',
                'status_code'
            ]);
    }
}
// ?content=test&author=John&page=1&pageSize=10