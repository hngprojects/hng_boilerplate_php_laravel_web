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
        $this->actingAs($this->user, 'api');

        // Create a product to update
        $this->product = Product::factory()->create();
    }

    /** @test */
    public function it_updates_a_product_with_valid_data()
    {
        $response = $this->putJson("/api/v1/products/{$this->product->id}", [
            'name' => 'Updated Product Name',
            'description' => 'Updated Product Description',
            'price' => 500.0,
            'tag' => 'New Tag'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $this->product->id,
            'name' => 'Updated Product Name',
            'description' => 'Updated Product Description',
            'price' => 500.0,
            'tag' => 'New Tag',
        ]);
    }

    /** @test */
    public function it_returns_404_if_product_not_found()
    {
        $response = $this->putJson('/api/v1/products/999', [
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

    /** @test */
    public function it_returns_400_if_validation_fails()
    {
        $response = $this->putJson("/api/v1/products/{$this->product->id}", [
            'name' => '',
            'description' => '',
            'price' => 'invalid',
            'tag' => ''
        ]);

        $response->assertStatus(422); // Laravel returns 422 for validation errors
        $response->assertJsonValidationErrors(['name', 'description', 'price', 'tag']);
    }

    /** @test */
    public function it_updates_only_provided_fields()
    {
        $response = $this->putJson("/api/v1/products/{$this->product->id}", [
            'tag' => 'New Tag'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $this->product->id,
            'name' => $this->product->name, // Unchanged
            'description' => $this->product->description, // Unchanged
            'price' => $this->product->price, // Unchanged
            'tag' => 'New Tag',
        ]);
    }
}
