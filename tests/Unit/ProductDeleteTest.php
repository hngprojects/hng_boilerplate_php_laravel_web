<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;


class ProductDeleteTest extends TestCase
{
    use RefreshDatabase;

    private function registerUser()
    {
        $user = [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson('/api/v1/auth/register', $user);
        $response->assertStatus(201);

        return $response->json('data.accessToken');
    }

    public function test_authenticated_user_can_create_product()
    {
        $token = $this->registerUser();
        $this->assertNotEmpty($token);

        $product = Product::factory()->make([
            'user_id' => auth()->id() // This ensures the product is associated with the registered user
        ])->toArray();

        $createProduct = $this->postJson('/api/v1/products', $product, [
            'Authorization' => "Bearer $token"
        ]);

        $createProduct->assertStatus(201);

        $this->assertDatabaseHas('products', [
            'name' => $product['name'],
            'description' => $product['description'],
            'price' => $product['price'] // Use the price from the factory
        ]);
    }

    public function test_authenticated_user_can_delete_product()
    {
        $token = $this->registerUser();
        $this->assertNotEmpty($token);

        $product = Product::factory()->create([
            'user_id' => auth()->id() // This ensures the product is associated with the registered user
        ]);

        $productId = $product->id;

        $deleteProduct = $this->deleteJson("/api/v1/products/{$productId}", [], [
            'Authorization' => "Bearer $token"
        ]);

        $deleteProduct->assertStatus(200);

        $this->assertDatabaseMissing('products', [
            'id' => $productId,
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price // Use the price from the factory
        ]);
    }

    public function test_unauthenticated_user_cannot_delete_product()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'user_id' => $user->id
        ]);

        $response = $this->deleteJson("/api/v1/products/{$product->id}");

        $response->assertStatus(401);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price // Use the price from the factory
        ]);
    }

    public function test_user_cannot_delete_non_existent_product()
    {
        $token = $this->registerUser();
        $this->assertNotEmpty($token);

        // Use a non-existent product UUID
        $nonExistentProductId = '123e4567-e89b-12d3-a456-426614174000'; // Ensure this ID does not exist

        $deleteProduct = $this->deleteJson("/api/v1/products/{$nonExistentProductId}", [], [
            'Authorization' => "Bearer $token"
        ]);

        $deleteProduct->assertStatus(404);

        $deleteProduct->assertJson([
            'message' => 'Product not found',
            'status_code' => 404,
        ]);
    }
}
