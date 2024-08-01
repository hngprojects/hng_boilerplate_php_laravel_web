<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class BlogControllerTest extends TestCase
{
    use RefreshDatabase;

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
                    '*' => ['title', 'content', 'image', 'blog_category_id', 'author', 'created_at'],
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
                    '*' => ['title', 'content', 'image', 'blog_category_id', 'author', 'created_at'],
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
        $token = JWTAuth::fromUser($superAdmin);

        // Act as the superadmin user with the generated token
        $this->withHeaders(['Authorization' => 'Bearer ' . $token])
                        ->deleteJson("/api/v1/blogs/{$blog->id}")
                        ->assertStatus(204);
    }

    public function test_non_superadmin_cannot_delete_blog()
    {
        // Create a normal user
        $user = User::factory()->create(['role' => 'user']);

          // Authenticate the user with a valid JWT token
        $token = JWTAuth::fromUser($user);

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
    }

    public function test_admin_can_create_blog_post()
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role'=>'admin']);
        $blog_category = BlogCategory::factory()->create();
        $token = JWTAuth::fromUser($admin);

        $image1 = UploadedFile::fake()->image('blog_image1.jpg');
        $image2 = UploadedFile::fake()->image('blog_image2.jpg');

        $author = 'John Doe';

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])->postJson('/api/v1/blogs', [
            'title' => 'Test Blog Post',
            'content' => 'This is a test blog post content.',
            'author' => $author,
            'images' => [$image1, $image2],
            'blog_category_id' => $blog_category->id,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Blog post created successfully.',
                'status_code' => 201,
            ]);

        $this->assertDatabaseHas('blogs', [
            'title' => 'Test Blog Post',
            'content' => 'This is a test blog post content.',
            'author' => $author,
            'blog_category_id' => $blog_category->id,
        ]);

        Storage::disk('public')->assertExists('images/' . $image1->hashName());
        Storage::disk('public')->assertExists('images/' . $image2->hashName());
    }

    public function test_blog_create_request_validation()
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role'=>'admin']);
        $token = JWTAuth::fromUser($admin);

        $invalidData = [
            'title' => '',
            'content' => '',
            'author' => '',
            'blog_category_id' => 12345,
        ];

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])->postJson('/api/v1/blogs', $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'content', 'author', 'blog_category_id']);

    }

    public function test_admin_can_create_blog_category()
    {
        // Create a user with admin role
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        
        $token = JWTAuth::fromUser($admin);

        // Data to create the blog category
        $data = [
            'name' => 'New category',
            'description' => 'This is a new Category',
        ];

        // Send a request to create the blog category
        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
                            ->json('POST', route('admin.blog-category.create'), $data)
                            ->assertJson([
                                'message' => 'Blog category created successfully.',
                                'status_code' => 201,
                            ]);
                
        $this->assertDatabaseHas('blog_categories', [
            'name' => 'New category',
            'description' => 'This is a new Category',
        ]);
    }

    /**
     * Test that a non-admin user cannot update the blog.
     */
    public function test_non_admin_cannot_create_blog_category()
    {
        // Create a user without admin role
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        // Log in the user
        $token = JWTAuth::fromUser($user);

        // Create a blog category to update
        $blog = Blog::factory()->create();

        // Data to update the blog category
        $data = [
            'name' => 'Updated Title',
            'description' => 'Updated Content',
        ];

        // Send a request to update the blog category
        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
                        ->json('PATCH', route('admin.blogs.update', ['id' => $blog->id]), $data);

        // Assert the response status
        $response->assertStatus(401);

        // Assert the blog category was not updated
        $this->assertDatabaseMissing('blog_categories', [
            'id' => $blog->id,
            'name' => 'Updated Title',
            'description' => 'Updated Content',
        ]);
    }
    
    public function test_admin_can_update_blog()
    {
        // Create a user with admin role
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        
        $token = JWTAuth::fromUser($admin);
        // Create a blog post to update
        $blog = Blog::factory()->create();

        // Fake the storage to test image uploads
        Storage::fake('public');

        // New images for the blog
        $images = [
            UploadedFile::fake()->image('image1.jpg'),
            UploadedFile::fake()->image('image2.jpg')
        ];

        // Data to update the blog post
        $data = [
            'title' => 'Updated Title',
            'content' => 'Updated Content',
            'author' => 'Updated Author',
            'images' => $images,
        ];

        // Send a request to update the blog post
        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
                            ->json('PATCH', route('admin.blogs.update', ['id' => $blog->id]), $data);

        // Assert the response status
        $response->assertStatus(200);

        // Assert the blog post was updated
        $this->assertDatabaseHas('blogs', [
            'id' => $blog->id,
            'title' => 'Updated Title',
            'content' => 'Updated Content',
            'author' => 'Updated Author',
        ]);

        // Assert the images were uploaded and associated with the blog post
        $uploadedImages = BlogImage::where('blog_id', $blog->id)->get();
        $this->assertCount(2, $uploadedImages);
        foreach ($uploadedImages as $uploadedImage) {
            Storage::disk('public')->assertExists($uploadedImage->image_url);
        }
    }

    /**
     * Test that a non-admin user cannot update the blog.
     */
    public function test_non_admin_cannot_update_blog()
    {
        // Create a user without admin role
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        // Log in the user
        $token = auth()->login($user);

        // Create a blog post to update
        $blog = Blog::factory()->create();

        // Data to update the blog post
        $data = [
            'title' => 'Updated Title',
            'content' => 'Updated Content',
            'author' => 'Updated Author',
            'images' => [UploadedFile::fake()->image('image1.jpg')],
        ];

        // Send a request to update the blog post
        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
                        ->json('PATCH', route('admin.blogs.update', ['id' => $blog->id]), $data);

        // Assert the response status
        $response->assertStatus(401);

        // Assert the blog post was not updated
        $this->assertDatabaseMissing('blogs', [
            'id' => $blog->id,
            'title' => 'Updated Title',
            'content' => 'Updated Content',
            'author' => 'Updated Author',
        ]);
    }
}
