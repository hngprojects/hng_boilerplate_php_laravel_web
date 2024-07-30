<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Blog;
use App\Models\Comment;
use Tymon\JWTAuth\Facades\JWTAuth;

class CommentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testCreateComment()
    {
        $user = User::factory()->create();
        $blog = Blog::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson("/api/v1/blogs/{$blog->id}/comments", [
                'content' => 'This is a test comment'
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 201,
                'message' => 'Comment created successfully',
                'data' => [
                    'content' => 'This is a test comment',
                    'blog_id' => $blog->id,
                    'user_id' => $user->id,
                ]
            ]);
    }


    public function testReplyComment()
    {
        $user = User::factory()->create();
        $blog = Blog::factory()->create();
        $comment = Comment::factory()->create(['blog_id' => $blog->id, 'user_id' => $user->id]);
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson("/api/v1/comments/{$comment->id}/reply", [
                'content' => 'This is a reply'
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 201,
                'message' => 'Reply created successfully',
                'data' => [
                    'content' => 'This is a reply',
                    'blog_id' => $blog->id,
                    'user_id' => $user->id,
                ]
            ]);
    }


    public function testLikeComment()
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $user->id]);
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson("/api/v1/comments/{$comment->id}/like");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 200,
                'message' => 'Comment liked successfully',
                'data' => [
                    'id' => $comment->id,
                    'likes' => 1,
                ]
            ]);
    }


    public function testDislikeComment()
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $user->id]);
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson("/api/v1/comments/{$comment->id}/dislike");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 200,
                'message' => 'Comment disliked successfully',
                'data' => [
                    'id' => $comment->id,
                    'dislikes' => 1,
                ]
            ]);
    }


    public function testEditComment()
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $user->id]);
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->patchJson("/api/v1/comments/edit/{$comment->id}", [
                'content' => 'Edited content'
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 200,
                'message' => 'Comment edited successfully',
                'data' => [
                    'id' => $comment->id,
                    'content' => 'Edited content',
                ]
            ]);
    }
    public function testDeleteComment()
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $user->id]);
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->deleteJson("/api/v1/comments/{$comment->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 200,
                'message' => 'Comment deleted successfully',
            ]);
    }


    public function testGetCommentsForBlog()
    {
        $user = User::factory()->create();
        $blog = Blog::factory()->create();
        Comment::factory()->create(['blog_id' => $blog->id, 'user_id' => $user->id]);
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->getJson("/api/v1/blogs/{$blog->id}/comments");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'user_id',
                        'blog_id',
                        'name',
                        'content',
                        'likes',
                        'dislikes',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);
    }

}
