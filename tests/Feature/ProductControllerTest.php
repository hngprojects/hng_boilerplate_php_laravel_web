<?php

namespace Tests\Feature;

// use App\Models\Category;
// use App\Models\Product;
// use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
// use Illuminate\Foundation\Testing\RefreshDatabase;
// use Illuminate\Foundation\Testing\WithFaker;
// use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Models\User;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // Create a user
        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'api');

        // Create categories
        $this->categories = Category::factory()->count(3)->create();

        // Create products and variants
        $this->products = Product::factory()->count(10)->create(['user_id' => $this->user->id]);

        foreach ($this->products as $product) {
            $product->categories()->attach($this->categories->random());
            ProductVariant::factory()->create(['product_id' => $product->product_id, 'stock_status' => 'in_stock']);
        }
    }

    /** @test */
    public function it_searches_products_with_all_filters()
    {
        $category = $this->categories->first()->name;
        $product = $this->products->first();

        $response = $this->getJson('/api/v1/products/search?name=' . $product->name . '&category=' . $category . '&minPrice=0&maxPrice=1000&status=in_stock');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'products' => [
                '*' => [
                    'name',
                    'price',
                    'imageUrl',
                    'description',
                    'product_id',
                    'quantity',
                    'category',
                    'stock',
                    'status',
                    'date_added',
                ],
            ],
            'statusCode',
        ]);
    }

    /** @test */
    public function it_searches_products_with_partial_filters()
    {
        $product = $this->products->first();

        $response = $this->getJson('/api/v1/products/search?name=' . $product->name);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'products' => [
                '*' => [
                    'name',
                    'price',
                    'imageUrl',
                    'description',
                    'product_id',
                    'quantity',
                    'category',
                    'stock',
                    'status',
                    'date_added',
                ],
            ],
            'statusCode',
        ]);
    }

    /** @test */
    public function it_returns_validation_errors_for_missing_required_fields()
    {
        $response = $this->getJson('/api/v1/products/search');

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'errors' => [
                ['parameter' => 'name', 'message' => 'The name field is required.']
            ],
            'statusCode' => 422
        ]);
    }

    /** @test */
    public function it_returns_validation_errors_for_invalid_status()
    {
        $response = $this->getJson('/api/v1/products/search?name=Test&status=invalid_status');

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'errors' => [
                ['parameter' => 'status', 'message' => 'The selected status is invalid.']
            ],
            'statusCode' => 422
        ]);
    }

    /** @test */
    public function it_returns_no_results_when_no_products_match()
    {
        $response = $this->getJson('/api/v1/products/search?name=NonexistentProduct');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'products' => [],
            'statusCode' => 200
        ]);
    }
}

