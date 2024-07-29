<?php
namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;
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
        $user = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'testuser@example.com',
            'password' => 'Ed8M7s*)?e:hTb^#&;C!<y',
            'password_confirmation' => 'Ed8M7s*)?e:hTb^#&;C!<y',

        ];

        $response = $this->postJson('/api/v1/auth/register', $user);

        // Ensure registration was successful
        $response->assertStatus(201);

        // Retrieve the JWT token from the registration response
        $token = $response->json('access_token');

        $this->assertNotEmpty($token);

        // Create a product as the authenticated user
        $this->actingAs(User::factory()->create());
        $product = [
            'name' => 'Test Product',
            'description' => 'Test description',
            'price' => 10000,
            'tags' => 'Test, Product',
            'status' => 'draft',
        ];

        $createProduct = $this->postJson('/api/v1/products', $product);

        // // Check the status code
        $createProduct->assertStatus(201);

        // // Assert that the product was created in the database
        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'description' => 'Test description'
        ]);
    }
}
