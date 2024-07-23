<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductDeleteTest extends TestCase
{
    use RefreshDatabase;

   
    /**
     * Test that an authenticated user can delete a product.
     */
    public function test_authenticated_user_can_delete_product()
    {
        // Register a user
        $user = [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson('/api/v1/auth/register', $user);

        // Ensure registration was successful
        $response->assertStatus(201);

        // Retrieve the JWT token from the registration response
        $token = $response->json('data.accessToken');

        $this->assertNotEmpty($token);

        // Create a product as the authenticated user
        $product = [
            'name' => 'Test Product',
            'description' => 'Test description'
        ];

        $createProduct = $this->postJson('/api/v1/products', $product, [
            'Authorization' => "Bearer $token"
        ]);

        // Check the status code
        $createProduct->assertStatus(201);

        // Retrieve the created product ID
        $productId = $createProduct->json('data.product_id');

        // Delete the product
        $deleteProduct = $this->deleteJson("/api/v1/products/{$productId}", [], [
            'Authorization' => "Bearer $token"
        ]);

        // Check the status code
        $deleteProduct->assertStatus(200);

        // Assert the product was deleted from the database
        $this->assertDatabaseMissing('products', [
            'id' => $productId,
            'name' => 'Test Product',
            'description' => 'Test description'
        ]);
    }

    /**
     * Test that an unauthenticated user cannot delete a product.
     */
    public function test_unauthenticated_user_cannot_delete_product()
    {
        // Create a user
        $user = User::factory()->create();

        // Create a product for the user
        $product = Product::factory()->create(['user_id' => $user->id]);

        // Attempt to delete the product without authentication
        $response = $this->deleteJson("/api/v1/products/{$product->id}");

        // Check the status code
        $response->assertStatus(401);

        // Assert the product still exists in the database
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description
        ]);
    }

    /**
     * Test that a user cannot delete a non-existent product.
     */
    public function test_user_cannot_delete_non_existent_product()
    {
        // Register a user
        $user = [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson('/api/v1/auth/register', $user);

        // Ensure registration was successful
        $response->assertStatus(201);

        // Retrieve the JWT token from the registration response
        $token = $response->json('data.accessToken');

        $this->assertNotEmpty($token);

        // Attempt to delete a non-existent product
        $deleteProduct = $this->deleteJson('/api/v1/products/999', [], [
            'Authorization' => "Bearer $token"
        ]);

        // Check the status code
        $deleteProduct->assertStatus(404);

        // Assert the appropriate error message is returned
        $deleteProduct->assertJson([
            'message' => 'Product not found',
            'status_code' => 404,
        ]);
    }
}
