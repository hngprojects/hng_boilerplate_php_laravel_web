<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Models\User;

class ProductSearchTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $categories;
    protected $products;

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

            // Ensure that the product_id is set when creating ProductVariant
            ProductVariant::factory()->create([
                'product_id' => $product->product_id,
                'stock_status' => 'in_stock',
                'stock' => 10,
            ]);
        }
    }

    /** @test */
    public function it_validates_the_search_request()
    {
        $response = $this->json('GET', '/api/v1/products/search', [
            'name' => '',
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success',
            'errors' => [
                ['parameter', 'message']
            ],
            'status_code'
        ]);
    }

    /** @test */
    public function it_returns_products_based_on_search_criteria()
    {
        $category = $this->categories->first()->name;
        $product = $this->products->first();

        // $response = $this->json('GET', '/api/v1/products/search', [
        //     'name' => $product->name,
        //     'category' => $category,
        //     'minPrice' => 10,
        //     'maxPrice' => 1000,
        //     'status' => 'in_stock',
        //     'page' => 1,
        //     'limit' => 10,
        // ]);
        $response = $this->getJson('/api/v1/products/search?name=' . $product->name . '&category=' . $category . '&minPrice=0&maxPrice=1000&status=in_stock&page=1&limit=10');

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
                    'date_added'
                ]
            ],
            'pagination' => [
                'totalItems',
                'totalPages',
                'currentPage',
                'perPage',
            ],
            'status_code'
        ]);

    }

    /** @test */
    public function it_paginates_the_results()
    {
        Product::factory()->count(20)->create(['name' => 'Laptop']);

        $response = $this->json('GET', '/api/v1/products/search', [
            'name' => 'Laptop',
            'page' => 2,
            'limit' => 5,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'products',
            'pagination' => [
                'totalItems',
                'totalPages',
                'currentPage',
                'perPage',
            ],
            'status_code'
        ]);

        $json = $response->json();
        $this->assertEquals(2, $json['pagination']['currentPage']);
        $this->assertEquals(5, $json['pagination']['perPage']);
    }

    /** @test */
    public function it_returns_validation_errors_for_missing_required_fields()
    {
        $response = $this->json('GET', '/api/v1/products/search');

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'errors' => [
                ['parameter' => 'name', 'message' => 'The name field is required.']
            ],
            'status_code' => 422
        ]);
    }

    /** @test */
    public function it_returns_validation_errors_for_invalid_status()
    {
        $response = $this->json('GET', '/api/v1/products/search?name=Test&status=invalid_status');

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'errors' => [
                ['parameter' => 'status', 'message' => 'The selected status is invalid.']
            ],
            'status_code' => 422
        ]);
    }

    /** @test */
    public function it_returns_no_results_when_no_products_match()
    {
        $response = $this->json('GET', '/api/v1/products/search?name=NonexistentProduct');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'products' => [],
            'status_code' => 200
        ]);
    }
}
