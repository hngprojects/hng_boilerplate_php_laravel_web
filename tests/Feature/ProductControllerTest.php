<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Product;
use App\Models\User;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;
    protected $user;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user for authentication
        $this->user = User::factory()->create();


        // Create a product to update
        $this->product = Product::factory()->create();
    }

    /** @test */
    public function test_updates_a_product_with_valid_data()
    {
        $this->actingAs($this->user, 'api');
        $updatedData = [
            'name' => 'Updated Product Name',
            'price' => 500,
            'description' => 'Updated Product Description',
            'tags' => 'New Tag',
            'slug' => 'updated-product-name',
            'imageUrl' => 'https://example.com/image.png'
        ];

        $response = $this->putJson("/api/v1/products/{$this->product->product_id}", $updatedData);

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $this->product->product_id,
            'name' => 'Updated Product Name',
            'price' => 500,
            'description' => 'Updated Product Description',
            'tag' => 'New Tag',
            'created_at' => $this->product->created_at->toISOString(),
            'updated_at' => $this->product->updated_at->toISOString()
        ]);

        // Verify the product has been updated in the database
        $this->assertDatabaseHas('products', [
            'product_id' => $this->product->product_id,
            'name' => 'Updated Product Name',
            'description' => 'Updated Product Description',
            'price' => 500,
            'tags' => 'New Tag',
            'slug' => 'updated-product-name',
            'imageUrl' => 'https://example.com/image.png'
        ]);
    }

    /** @test */
    public function test_returns_404_if_product_not_found()
    {
        $this->actingAs($this->user, 'api');
        $this->product->delete();
        $response = $this->putJson("/api/v1/products/{$this->product->product_id}", [
            'name' => 'Updated Product Name',
            'description' => 'Updated Product Description',
            'price' => 500.0,
            'tag' => 'New Tag'
        ]);

        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'Product not found',
            'error' => 'Not Found',
            'status_code' => 404
        ]);
    }

    // /** @test */
    public function test_returns_422_if_validation_fails()
    {
        $this->actingAs($this->user, 'api');
        $response = $this->putJson("/api/v1/products/{$this->product->product_id}", [
            'name' => '',
            'description' => '',
            'price' => 'invalid',
            'tag' => ''
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'status_code',
                'message',
                'error'
            ]);
    }


    // /** @test */
    public function test_updates_only_provided_fields()
    {
        $this->actingAs($this->user, 'api');
        $response = $this->putJson("/api/v1/products/{$this->product->product_id}", [
            'tags' => 'New Tag'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $this->product->product_id,
            'name' => $this->product->name, // Unchanged
            'description' => $this->product->description, // Unchanged
            'price' => (float) $this->product->price, // Unchanged
            'tag' => 'New Tag',
            'created_at' => $this->product->created_at->toISOString(),
            'updated_at' => $this->product->updated_at->toISOString()
        ]);
    }

    /** @test */
    public function test_requires_authentication_to_update_product()
    {
        $product = Product::factory()->create();
        $response = $this->putJson("/api/v1/products/{$product->product_id}", [
            'name' => 'Updated Product Name',
            'description' => 'Updated Product Description',
            'price' => 500,
            'tag' => 'New Tag'
        ]);

        $response->assertStatus(401);
    }
}
