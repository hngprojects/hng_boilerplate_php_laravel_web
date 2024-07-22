<?php
namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductCreationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that only authenticated user can create a product.
     */
    public function test_authenticated_user_can_create_product()
    {
        // Register a user
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => Hash::make('password'),
        ]);

        // Retrieve the JWT token from the login response
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'testuser@example.com',
            'password' => 'password'
        ]);

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

        // // Check the status code
        $createProduct->assertStatus(201);

        // // Assert that the product was created in the database
        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'description' => 'Test description'
        ]);
    }
}
