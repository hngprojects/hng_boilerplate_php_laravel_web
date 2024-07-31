<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Models\User;
use App\Models\Organisation;

class ProductIndexTest extends TestCase
{
     use RefreshDatabase;

    // public function setUp(): void
    // {
    //     parent::setUp();

    //     // Create user and products
    //     $user = User::factory()->create();
    //     $this->actingAs($user, 'api');

    //     $products = Product::factory()->count(50)->create(['user_id' => $user->id]);

    //     // Create categories and associate with products
    //     $categories = Category::factory()->count(5)->create();
    //     foreach ($products as $product) {
    //         $product->categories()->attach($categories->random());
    //     }

        // Create product variants and associate with products
        foreach ($products as $product) {
            ProductVariant::factory()->create(['product_id' => $product->product_id]);
        }
    }

    // /** @test */
    // public function it_validates_pagination_parameters()
    // {
    //     $response = $this->getJson('/api/v1/products?page=0&limit=10');

    //     $response->assertStatus(400);
    //     $response->assertJson([
    //         'status' => 'bad request',
    //         'message' => 'Invalid query params passed',
    //         'status_code' => 400,
    //     ]);
    // }

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

    protected $user;
    protected $organisation;
    protected $products;

    public function setUp(): void
    {
        parent::setUp();

        // Create user and organisations
        $this->user = User::factory()->create();
        $this->organisation = Organisation::factory()->create();
        
        // Attach user to organisation
        $this->user->organisations()->attach($this->organisation->id);

        // Acting as the user
        $this->actingAs($this->user, 'api');

        // Create products for the organisation
        $this->products = Product::factory()->count(10)->create(['org_id' => $this->organisation->org_id]);

        // Create categories and associate with products
        $categories = Category::factory()->count(5)->create();
        foreach ($this->products as $product) {
            $product->categories()->attach($categories->random());
        }

        // Create product variants and associate with products
        foreach ($this->products as $product) {
            ProductVariant::factory()->create(['product_id' => $product->product_id]);
        }
    }

    /** @test */
    // public function it_fetches_products_for_authorised_users()
    // {
    //     $response = $this->getJson('/api/v1/products/' . $this->organisation->org_id . '?page=1&limit=10');
    //     $response->assertStatus(200);
    //     $response->assertJsonStructure([
    //         'success',
    //         'message',
    //         'products' => [
    //             '*' => [
    //                 'name',
    //                 'price',
    //                 'imageUrl',
    //                 'description',
    //                 'product_id',
    //                 'quantity',
    //                 'category',
    //                 'stock',
    //                 'status',
    //                 'date_added',
    //             ],
    //         ],
    //         'pagination' => [
    //             'totalItems',
    //             'totalPages',
    //             'currentPage',
    //             'perPage',
    //         ],
    //         'status_code',
    //     ]);
    // }

    /** @test */
    public function it_prevents_unauthorised_users_from_fetching_products()
    {
        $otherOrganisation = Organisation::factory()->create();
        $response = $this->getJson('/api/v1/products/' . $otherOrganisation->org_id . '?page=1&limit=10');

        $response->assertStatus(403);
        $response->assertJson([
            'status' => 'error',
            'message' => 'Unauthorized: You are not a member of this organisation',
            'status_code' => 403,
        ]);
    }


}
