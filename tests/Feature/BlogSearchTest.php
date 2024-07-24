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

        $response = $this->getJson('/api/v1/blogs/search?content=test&page=1&pageSize=10');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'currentPage',
                'totalPages',
                'totalResults',
                'blogs' => [
                    '*' => [
                        'id',
                        'title',
                        'content',
                        'author',
                        'createdDate',
                        'tags',
                    ]
                ],
                'meta' => [
                    'hasNext',
                    'total',
                    'nextPage',
                    'prevPage',
                ]
            ]);
    }
}
