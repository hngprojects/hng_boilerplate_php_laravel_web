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

        $response = $this->deleteJson('/api/v1/blogs/' . $blog->id);

        $response->assertStatus(403)
                 ->assertJson([
                     'message' => 'You are not authorized to perform this action',
                     'status_code' => 403
                 ]);
    }

    public function test_authenticated_user_can_delete_blog_post()
    {
        $user = User::factory()->create();
        $blog = Blog::factory()->create(['author' => $user->name]);

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->deleteJson('/api/v1/blogs/' . $blog->id);

        $response->assertStatus(202)
                 ->assertJson([
                     'message' => 'Blog successfully deleted',
                     'status_code' => 202
                 ]);

        // Check if the blog post is deleted from the database
        $this->assertDatabaseMissing('blogs', ['id' => $blog->id]);
    }

    public function test_authenticated_user_cannot_delete_non_existent_blog_post()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->deleteJson('/api/v1/blogs/non-existent-id');

        $response->assertStatus(404)
                 ->assertJson([
                     'message' => 'Blog with the given Id does not exist',
                     'status_code' => 404
                 ]);
    }

    // public function test_internal_server_error()
    // {
    //     $user = User::factory()->create();
    //     $token = JWTAuth::fromUser($user);

    //     $this->mock(Blog::class, function ($mock) {
    //         $mock->shouldReceive('findOrFail')->andThrow(new \Exception('Mock exception'));
    //     });

    //     $response = $this->withHeader('Authorization', 'Bearer ' . $token)
    //                      ->deleteJson('/api/blogs/' . 'some-id');

    //     $response->assertStatus(500)
    //              ->assertJson([
    //                  'message' => 'Internal server error.',
    //                  'status_code' => 500
    //              ]);
    // }

    // public function test_invalid_id_parameters()
    // {
    //     $user = User::factory()->create();
    //     $token = JWTAuth::fromUser($user);

    //     $response = $this->withHeader('Authorization', 'Bearer ' . $token)
    //                      ->deleteJson('/api/blogs/' . 'invalid-id');

    //     $response->assertStatus(400)
    //              ->assertJson([
    //                  'message' => 'An invalid request was sent.',
    //                  'status_code' => 400
    //              ]);
    // }

    public function test_invalid_method()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->postJson('/api/v1/blogs/' . 'some-id');

        $response->assertStatus(405)
                 ->assertJson([
                     'message' => 'This method is not allowed.',
                     'status_code' => 405
                 ]);
    }
}
