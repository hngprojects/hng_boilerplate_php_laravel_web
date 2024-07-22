<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class BlogTest extends TestCase
{
    use RefreshDatabase;

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
             ->assertStatus(202)
             ->assertJson(['message' => 'Blog successfully deleted']);
    }

    public function test_non_superadmin_cannot_delete_blog()
    {
        // Create a normal user
        $user = User::factory()->create(['role' => 'user']);
        
        // Create a blog post
        $blog = Blog::factory()->create();

        // Act as the normal user
        $this->actingAs($user, 'api');

        // Send delete request and assert status
        $this->deleteJson("/api/v1/blogs/{$blog->id}")
            ->assertStatus(403); // Forbidden
    }
}
