<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HelpArticleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */ 
    // public function it_should_register_login_create_update_and_delete_help_article()
    // {
    //     // Step 1: Register a new user
    //     $registerResponse = $this->postJson('/api/v1/auth/register', [
    //         'first_name' => 'precious',
    //         'last_name' => 'test',
    //         'email' => 'precious@test.com',
    //         'password' => '120oklsQQMNu)',
    //     ]);

    //     $registerResponse->assertStatus(201);

    //     // Step 2: Login with the registered user
    //     $loginResponse = $this->postJson('/api/v1/auth/login', [
    //         'email' => 'precious@test.com',
    //         'password' => '120oklsQQMNu)',
    //     ]);

    //     $loginResponse->assertStatus(200)
    //         ->assertJson([
    //             'message' => 'Login successful',
    //         ]);

    //     $accessToken = $loginResponse['data']['access_token'];
    //     $userId = $loginResponse['data']['user']['id'];

    //     // Step 3: Create a help article
    //     $createArticleResponse = $this->withHeader('Authorization', 'Bearer ' . $accessToken)
    //         ->postJson('/api/v1/help-center/topics', [
    //             'user_id' => $userId,
    //             'title' => 'A Good Title ss',
    //             'content' => 'A good content',
    //         ]);

    //     $createArticleResponse->assertStatus(201)
    //         ->assertJson([
    //             'success' => true,
    //             'message' => 'Help article created successfully.',
    //             'data' => [
    //                 'user_id' => $userId,
    //                 'title' => 'A Good Title ss',
    //                 'content' => 'A good content',
    //             ],
    //         ]);

    //     $articleId = $createArticleResponse['data']['article_id'];

    //     // Step 4: Update the help article
    //     $updateArticleResponse = $this->withHeader('Authorization', 'Bearer ' . $accessToken)
    //         ->patchJson('/api/v1/help-center/topics/' . $articleId, [
    //             'title' => 'Updated Blazer',
    //             'content' => 'Updated content of the Blazing article...',
    //         ]);

    //     $updateArticleResponse->assertStatus(200)
    //         ->assertJson([
    //             'success' => true,
    //             'message' => 'Help article updated successfully.',
    //             'data' => [
    //                 'article_id' => $articleId,
    //                 'user_id' => $userId,
    //                 'title' => 'Updated Blazer',
    //                 'content' => 'Updated content of the Blazing article...',
    //             ],
    //         ]);

    //     // Step 5: Delete the help article
    //     $deleteArticleResponse = $this->withHeader('Authorization', 'Bearer ' . $accessToken)
    //         ->deleteJson('/api/v1/help-center/topics/' . $articleId);

    //     $deleteArticleResponse->assertStatus(200)
    //         ->assertJson([
    //             'success' => true,
    //             'message' => 'Help article deleted successfully.',
    //         ]);
    // }
}
