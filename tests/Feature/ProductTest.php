<?php
namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Product;

class ProductTest extends TestCase
{
    use RefreshDatabase;



    /**
     * Test that authenticated user can retrieve products with pagination.
     */
    public function test_authenticated_user_can_retrieve_products_with_pagination()
    {
        // Register a user
        $user = [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'Ed8M7s*)?e:hTb^#&;C!<y',
            'password_confirmation' => 'Ed8M7s*)?e:hTb^#&;C!<y',
            'first_name' => 'Test',
            'last_name' => 'User',
        ];

        $response = $this->postJson('/api/v1/auth/register', $user);

        // Ensure registration was successful
        $response->assertStatus(201);

        // Retrieve the JWT token from the registration response
        $token = $response->json('access_token');

        $this->assertNotEmpty($token);

        // Create 15 products
        Product::factory()->count(15)->create();

        // Test retrieving the first page with 10 products
        $response = $this->getJson('/api/v1/products?page=1&limit=10', [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'products' => [
                    '*' => ['name', 'price']
                ],
                'pagination' => ['totalItems', 'totalPages', 'currentPage'],
                'status_code'
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Products retrieved successfully',
                'pagination' => [
                    'totalItems' => 15,
                    'totalPages' => 2,
                    'currentPage' => 1,
                ],
                'status_code' => 200,
            ]);

        // Ensure the products are returned correctly
        $this->assertCount(10, $response->json('products'));
    }

    /**
     * Test that authenticated user receives bad request for invalid pagination params.
     */
    public function test_authenticated_user_receives_bad_request_for_invalid_pagination_params()
    {
        // Register a user
        $user = [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'Ed8M7s*)?e:hTb^#&;C!<y',
            'password_confirmation' => 'Ed8M7s*)?e:hTb^#&;C!<y',
            'first_name' => 'Test',
            'last_name' => 'User',
        ];

        $response = $this->postJson('/api/v1/auth/register', $user);

        // Ensure registration was successful
        $response->assertStatus(201);

        // Retrieve the JWT token from the registration response
        $token = $response->json('access_token');

        $this->assertNotEmpty($token);

        // Attempt to retrieve products with invalid pagination params
        $response = $this->getJson('/api/v1/products?page=invalid&limit=10', [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'status' => 'bad request',
                'message' => 'Invalid query params passed',
                'status_code' => 400,
            ]);
    }


}
