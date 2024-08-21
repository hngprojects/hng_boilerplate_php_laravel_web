<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Product;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that authenticated user can retrieve products with pagination.
     */
    public function test_authenticated_user_can_retrieve_products_with_pagination()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        Product::factory()->count(15)->create();

        $response = $this->getJson('/api/v1/products?page=1&limit=10', [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => ['products'],
                'pagination' => ['totalItems', 'totalPages', 'currentPage'],
                'status_code'
            ])
            ->assertJson([
                'message' => 'Products retrieved successfully',
                'pagination' => [
                    'totalItems' => 15,
                    'totalPages' => 2,
                    'currentPage' => 1,
                ],
                'status_code' => 200,
            ]);

        // Ensure the products are returned correctly
        $this->assertCount(10, $response->json('data.products'));
    }

    /**
     * Test that authenticated user receives bad request for invalid pagination params.
     */
    public function test_authenticated_user_receives_bad_request_for_invalid_pagination_params()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

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
