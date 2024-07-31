<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Models\User;

class ProductIndexTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // Create user and products
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $products = Product::factory()->count(50)->create(['user_id' => $user->id]);

        // Create categories and associate with products
        $categories = Category::factory()->count(5)->create();
        foreach ($products as $product) {
            $product->categories()->attach($categories->random());
        }

        // Create product variants and associate with products
        foreach ($products as $product) {
            ProductVariant::factory()->create(['product_id' => $product->product_id]);
        }
    }

    /** @test */
    public function it_validates_pagination_parameters()
    {
        $response = $this->getJson('/api/v1/products?page=0&limit=10');

        $response->assertStatus(400);
        $response->assertJson([
            'status' => 'bad request',
            'message' => 'Invalid query params passed',
            'status_code' => 400,
        ]);
    }

    /** @test */
    // public function it_handles_internal_server_error()
    // {
    //     // Mock the Product model to throw an exception
    //     $this->mock(Product::class, function ($mock) {
    //         $mock->shouldReceive('select')->andThrow(new \Exception('Internal server error'));
    //     });

    //     $response = $this->getJson('/api/v1/products?page=1&limit=10');

    //     $response->assertStatus(500);
    //     $response->assertJson([
    //         'status' => 'error',
    //         'message' => 'Internal server error',
    //         'status_code' => 500,
    //     ]);
    // }
}
