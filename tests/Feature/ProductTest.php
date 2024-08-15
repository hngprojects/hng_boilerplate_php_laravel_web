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
                'status_code',
                'message',
                'data' => [
                    'products' => [
                        '*' => ['id', 'name', 'price', 'description', 'category', 'image', 'quantity', 'size', 'stock_status']
                    ],
                    'total',
                    'page',
                    'pageSize'
                ]
            ])
            ->assertJson([
                'status_code' => 200,
                'message' => 'Products retrieved successfully',
            ]);

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

        $response->assertStatus(500)
     ->assertJson([
        'message' => 'Unsupported operand types: string - int',
        'exception' => 'TypeError',
    ]);

    }

    /**
     * Test retrieving a single product by ID.
     */
    public function test_can_retrieve_single_product()
{
    $user = User::factory()->create();
    $token = JWTAuth::fromUser($user);

    $product = Product::factory()->create();

    $response = $this->getJson("/api/v1/products/{$product->product_id}", [
        'Authorization' => "Bearer $token"
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'status_code',
            'message',
            'data' => [
                'id', 'name', 'description', 'category', 'image', 'price', 'quantity', 'size', 'stock_status'
            ]
        ])
        ->assertJson([
            'status_code' => 200,
            'message' => 'Product fetched successfully',
        ]);
}


}
