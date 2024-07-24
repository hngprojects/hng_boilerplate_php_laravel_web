<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use LazilyRefreshDatabase;
    
    public function test_search_products_by_name()
    {
        $product = Product::factory()->create(['name' => 'Test Product']);

        $response = $this->get('/api/v1/products/search?name=Test');

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Test Product']);
    }

    public function test_search_products_by_category()
    {
        $category = Category::factory()->create(['name' => 'Test Category']);
        $product = Product::factory()->create();
        $product->categories()->attach($category->id);

        $response = $this->get('/api/v1/products/search?name=Product&category=Test Category');

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Test Category']);
    }

    public function test_search_products_by_price_range()
    {
        $product = Product::factory()->create(['name' => 'Test Product', 'price' => 150]);

        $response = $this->get('/api/v1/products/search?name=Test&minPrice=100&maxPrice=200');

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Test Product']);
    }

    public function test_search_returns_empty_for_nonexistent_name()
    {
        $response = $this->get('/api/v1/products/search?name=Nonexistent');

        $response->assertStatus(204);
    }

    public function test_search_validation_error()
    {
        $response = $this->get('/api/v1/products/search');

        $response->assertStatus(422);
        $response->assertJsonFragment(['parameter' => 'name', 'message' => 'name is a required parameter']);
    }
}