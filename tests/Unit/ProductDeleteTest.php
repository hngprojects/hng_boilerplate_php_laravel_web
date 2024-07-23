<?php
namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductDeleteTest extends TestCase
{
    use RefreshDatabase;
    public function test_authenticated_user_can_create_product()
{
    $user = [
        'name' => 'Test User',
        'email' => 'testuser@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ];

    $response = $this->postJson('/api/v1/auth/register', $user);

    $response->assertStatus(201);

    $token = $response->json('data.accessToken');

    $this->assertNotEmpty($token);

    $product = [
        'name' => 'Test Product',
        'description' => 'Test description'
    ];

    $createProduct = $this->postJson('/api/v1/products', $product, [
        'Authorization' => "Bearer $token"
    ]);

    $createProduct->assertStatus(201);

    $this->assertDatabaseHas('products', [
        'name' => 'Test Product',
        'description' => 'Test description'
    ]);
}

public function test_authenticated_user_can_delete_product()
{
    $user = [
        'name' => 'Test User',
        'email' => 'testuser@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ];

    $response = $this->postJson('/api/v1/auth/register', $user);

    $response->assertStatus(201);

    $token = $response->json('data.accessToken');

    $this->assertNotEmpty($token);

    $product = [
        'name' => 'Test Product',
        'description' => 'Test description'
    ];

    $createProduct = $this->postJson('/api/v1/products', $product, [
        'Authorization' => "Bearer $token"
    ]);

    $createProduct->assertStatus(201);

    $productId = $createProduct->json('data.product_id');

    $deleteProduct = $this->deleteJson("/api/v1/products/{$productId}", [], [
        'Authorization' => "Bearer $token"
    ]);

    $deleteProduct->assertStatus(200);

    $this->assertDatabaseMissing('products', [
        'id' => $productId,
        'name' => 'Test Product',
        'description' => 'Test description'
    ]);
}

public function test_unauthenticated_user_cannot_delete_product()
{
    $user = User::factory()->create();
    $product = Product::factory()->create(['user_id' => $user->id]);

    $response = $this->deleteJson("/api/v1/products/{$product->id}");

    $response->assertStatus(401);

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

        // Use a non-existent product UUID
        $nonExistentProductId = '123e4567-e89b-12d3-a456-426614174000';

        // Attempt to delete a non-existent product
        $deleteProduct = $this->deleteJson("/api/v1/products/{$nonExistentProductId}", [], [
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
