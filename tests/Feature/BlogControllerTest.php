<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class BlogControllerTest extends TestCase
{

    use LazilyRefreshDatabase;

    /** @test */
    public function it_fetches_paginated_latest_blog_posts_without_parameters()
    {
        // Create some blog posts
        Blog::factory()->count(15)->create();


        // Send a request without pagination parameters
        $response = $this->getJson('/api/v1/blogs/latest');

        // Assert response status and structure
        $response->assertStatus(200)
            ->assertJsonStructure([
                'count',
                'next',
                'previous',
                'results' => [
                    '*' => ['title', 'content', 'images', 'tags', 'author', 'created_at'],
                ],
            ]);

        // Assert the default pagination values
        $response->assertJson([
            'count' => 15,
            'previous' => null,
        ]);

        // Extract the 'next' URL
        $nextUrl = $response->json('next');

        // Assert the 'next' URL contains the expected pagination parameters
        $this->assertStringContainsString('page=2', $nextUrl);
        $this->assertStringContainsString('page_size=10', $nextUrl);
    }

    /** @test */
    public function it_fetches_paginated_latest_blog_posts_with_parameters()
    {
        // Create some blog posts
        Blog::factory()->count(20)->create();

        // Send a request with pagination parameters
        $response = $this->getJson('/api/v1/blogs/latest?page=2&page_size=5');

        // Assert response status and structure
        $response->assertStatus(200)
            ->assertJsonStructure([
                'count',
                'next',
                'previous',
                'results' => [
                    '*' => ['title', 'content', 'images', 'tags', 'author', 'created_at'],
                ],
            ]);

        // Assert the custom pagination values
        $response->assertJson([
            'count' => 20,
        ]);

        // Extract the 'previous' and 'next' URLs
        $previousUrl = $response->json('previous');
        $nextUrl = $response->json('next');

        // Assert the 'previous' and 'next' URLs contain the expected pagination parameters
        $this->assertStringContainsString('page=1', $previousUrl);
        $this->assertStringContainsString('page_size=5', $previousUrl);
        $this->assertStringContainsString('page=3', $nextUrl);
        $this->assertStringContainsString('page_size=5', $nextUrl);
    }

    /** @test */
    public function it_handles_invalid_pagination_parameters()
    {

        // Send a request with invalid pagination parameters
        $response = $this->getJson('/api/v1/blogs/latest?page=-1&page_size=abc');

        // Assert response status and structure
        $response->assertStatus(400)
            ->assertJson([
                'error' => 'Invalid page or page_size parameter.',
            ]);
    }

    /** @test */
    public function it_handles_no_blog_posts_present()
    {

        // Send a request when no blog posts are present
        $response = $this->getJson('/api/v1/blogs/latest');

        // Assert response status and structure
        $response->assertStatus(200)
            ->assertJson([
                'count' => 0,
                'next' => null,
                'previous' => null,
                'results' => [],
            ]);
    }

    public function test_superadmin_can_delete_blog()
    {
        // Create a superadmin user
        $superAdmin = User::factory()->create(['role' => 'admin']);
    
        // Create a blog post
        $blog = Blog::factory()->create();
    
        // Generate a JWT token for the superadmin user
        $token = \Tymon\JWTAuth\Facades\JWTAuth::fromUser($superAdmin);
    
        // Act as the superadmin user with the generated token
        $this->withHeaders(['Authorization' => 'Bearer ' . $token])
             ->deleteJson("/api/v1/blogs/{$blog->id}")
             ->assertStatus(204)
             ->assertJson(['message' => 'Blog successfully deleted']);
    }

    public function test_non_superadmin_cannot_delete_blog()
    {
        // Create a normal user
        $user = User::factory()->create(['role' => 'user']);
        
          // Authenticate the user with a valid JWT token
        $token = auth()->login($user);

        // Create a blog post
        $blog = Blog::factory()->create();

        // Send delete request with the token and assert status
        $this->withHeaders(['Authorization' => "Bearer $token"])
            ->deleteJson("/api/v1/blogs/{$blog->id}")
            ->assertStatus(401); // Forbidden
    }
    public function test_blog_creation_endpoint_is_protected()
    {
        $response = $this->postJson('/api/v1/blogs', [

        ]);
        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Unauthenticated.'
        ]);
    }

    public function test_admin_can_create_blog_post()
    {
        Storage::fake('public');

        $admin = User::factory()->create();
        $this->actingAs($admin);

        $image1 = UploadedFile::fake()->image('blog_image1.jpg');
        $image2 = UploadedFile::fake()->image('blog_image2.jpg');

        $response = $this->postJson('/api/v1/blogs', [
            'title' => 'Test Blog Post',
            'content' => 'This is a test blog post content.',
            'author' => 'John Doe',
            'images' => [$image1, $image2],
            'tags' => [
                ['name' => 'Technology'],
                ['name' => 'Programming'],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Blog post created successfully.',
                'status_code' => 201,
            ]);

        $this->assertDatabaseHas('blogs', [
            'title' => 'Test Blog Post',
            'content' => 'This is a test blog post content.',
            'author' => 'John Doe',
        ]);

        $this->assertDatabaseHas('blog_tags', [
            'name' => 'Technology',
        ]);

        $this->assertDatabaseHas('blog_tags', [
            'name' => 'Programming',
        ]);

        Storage::disk('public')->assertExists('blog_header/' . $image1->hashName());
        Storage::disk('public')->assertExists('blog_header/' . $image2->hashName());
    }

    public function test_blog_create_request_validation()
    {
        Storage::fake('public');

        $admin = User::factory()->create();
        $this->actingAs($admin);

        $invalidData = [
            'title' => '',
            'content' => '',
            'author' => '',
            'images' => ['not_an_image'],
            'tags' => 'not_an_array',
        ];

        $response = $this->postJson('/api/v1/blogs', $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'content', 'author', 'images.0', 'tags']);

    }
}
