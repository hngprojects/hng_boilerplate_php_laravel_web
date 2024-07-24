<?php
namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
 
class ProductCreationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that only authenticated user can create a product.
     */
    public function test_authenticated_user_can_create_product()
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

        $name = 'Test Product',
        $product = [
            'name' => $name',
            'description' => 'Test description',
            'price' => 100.00,
            'slug' => Str::slug($name),
            'tags' => 'tag1, tag2',
            'imageUrl' => 'https://lorempixel.com/640/480'
        ];

        $createProduct = $this->postJson('/api/v1/products', $product, [
            'Authorization' => "Bearer $token"
        ]);

        // // Check the status code
        $createProduct->assertStatus(201);

        // // Assert that the product was created in the database
        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'description' => 'Test description'
        ]);
    }
}
