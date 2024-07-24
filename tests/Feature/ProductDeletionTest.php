<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

class ProductDeletionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that unauthenticated user cannot delete a product.
     */
    public function test_unauthenticated_user_cannot_delete_product()
    {
        // Create a product in the database
        $product = \App\Models\Product::factory()->create();

        // Attempt to delete the product without authentication
        $response = $this->deleteJson('/api/v1/products/' . $product->product_id);

        // Ensure the response status is 401 Unauthorized
        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Unauthenticated.',
        ]);
    }

    /**
     * Test that authenticated user can delete a product.
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
        $product = \App\Models\Product::factory()->create();

        // Attempt to delete the product as the authenticated user
        Log::info($product);
        $deleteProduct = $this->deleteJson('/api/v1/products/' . $product->product_id, [], [
            'Authorization' => "Bearer $token"
        ]);

        // Ensure the response status is 200 OK
        $deleteProduct->assertStatus(200);
        $deleteProduct->assertJson([
            'message' => 'Product deleted successfully.'
        ]);

        // Ensure the product was deleted from the database
        $this->assertDatabaseMissing('products', [
            'product_id' => $product->product_id
        ]);
    }

    /**
     * Test that authenticated user cannot delete a non-existent product.
     */
    public function test_authenticated_user_cannot_delete_non_existent_product()
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

        // Attempt to delete a non-existent product as the authenticated user
        $deleteProduct = $this->deleteJson('/api/v1/products/9c983281-e897-4130-ac90-611cedd3b3b1', [], [
            'Authorization' => "Bearer $token"
        ]);

        // Ensure the response status is 404 Not Found
        $deleteProduct->assertStatus(404);
        $deleteProduct->assertJson([
            'error' => 'Product not found',
            'message' => 'The product with ID 9c983281-e897-4130-ac90-611cedd3b3b1 does not exist.'
        ]);
    }
}
