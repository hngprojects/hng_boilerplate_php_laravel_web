<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Blog;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class BlogDeleteControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    public function test_unauthenticated_user_cannot_delete_blog_post()
    {
        $blog = Blog::factory()->create();

        $response = $this->deleteJson('/api/blogs/' . $blog->id);

        $response->assertStatus(401)
                 ->assertJson(['message' => 'Token not provided']);
    }

    public function test_authenticated_user_can_delete_blog_post()
    {
        $user = User::factory()->create();
        $blog = Blog::factory()->create(['author' => $user->name]);

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->deleteJson('/api/blogs/' . $blog->id);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Blog post deleted successfully']);

        // Check if the blog post is deleted from the database
        $this->assertDatabaseMissing('blogs', ['id' => $blog->id]);
    }

    public function test_authenticated_user_cannot_delete_non_existent_blog_post()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->deleteJson('/api/blogs/non-existent-id');

        $response->assertStatus(404)
                 ->assertJson(['error' => 'Blog post not found']);
    }
}
