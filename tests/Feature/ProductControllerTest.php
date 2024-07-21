<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testDeleteProductSuccessfully()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'api')->deleteJson("/api/v1/products/{$product->product_id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Product deleted successfully.']);

        $this->assertDatabaseMissing('products', ['product_id' => $product->product_id]);
    }

    public function testDeleteNonExistentProduct()
    {
        $user = User::factory()->create();
        $nonExistentProductId = 'non-existent-id';

        $response = $this->actingAs($user, 'api')->deleteJson("/api/v1/products/{$nonExistentProductId}");

        $response->assertStatus(404)
            ->assertJson([
                'error' => 'Product not found',
                'message' => "The product with ID {$nonExistentProductId} does not exist."
            ]);
    }

    public function testDeleteProductUnauthorized()
    {
        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/v1/products/{$product->product_id}");

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }
}
