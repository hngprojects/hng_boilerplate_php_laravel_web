<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Organisation;
use App\Models\OrganisationUser;
use App\Models\ProductComment;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductCommentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a new product comment.
     */
    public function test_it_can_create_a_new_product_comment(): void
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create an organisation
        $organisation = Organisation::factory()->create();

        // Associate the user with the organization as an owner
        OrganisationUser::create([
            'org_id' => $organisation->org_id,
            'user_id' => $user->id,
        ]);

        // Create a product
        $product = Product::factory()->create([
            'org_id' => $organisation->org_id,
            'user_id' => $user->id,
        ]);

        // Create a product comment
        $response = $this->postJson("/api/v1/organisations/{$organisation->org_id}/products/{$product->product_id}/comments", [
            'product_id' => $product->product_id,
            'comment' => 'This is a test comment',
        ]);

        // Assert the response status and structure
        $response->assertStatus(201 )
            ->assertJson([
                'status_code' => 201,
                'message' => 'Comment added successfully',
            ]);

        // Assert the comment exists in the database
        $this->assertDatabaseHas('product_comments', [
            'product_id' => $product->product_id,
            'comment' => 'This is a test comment',
        ]);
    }

    /**
     * Test updating a product comment.
     */
    public function test_it_can_update_product_comment(): void
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create an organisation
        $organisation = Organisation::factory()->create();

        // Associate the user with the organization as an owner
        OrganisationUser::create([
            'org_id' => $organisation->org_id,
            'user_id' => $user->id,
        ]);

        // Create a product
        $product = Product::factory()->create([
            'org_id' => $organisation->org_id,
            'user_id' => $user->id,
        ]);

        // Create a product comment
        $comment = ProductComment::create([
            'product_id' => $product->product_id,
            'user_id' => $user->id,
            'comment' => 'Initial comment',
        ]);

        // Update the product comment
        $response = $this->putJson("/api/v1/organisations/{$product->product_id}/comments/{$comment->id}", [
            'comment' => 'Updated comment',
        ]);

        // Assert the response status and structure
        $response->assertStatus(200)
            ->assertJson([
                'status_code' => 200,
                'message' => 'Comment updated successfully',
            ]);

        // Assert the comment was updated in the database
        $this->assertDatabaseHas('product_comments', [
            'id' => $comment->id,
            'comment' => 'Updated comment',
        ]);
    }
}
