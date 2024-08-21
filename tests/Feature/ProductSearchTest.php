<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use App\Models\Organisation;
use App\Models\OrganisationUser;
use App\Models\User;

class ProductSearchTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $organisation;
    protected $categories;
    protected $products;

    public function setUp(): void
    {
        parent::setUp();

        // Create a user and authenticate
        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        // Create an organisation and get the first instance
        $this->organisation = Organisation::factory()->create();

        // Associate the user with the organization as an owner
        OrganisationUser::create([
            'org_id' => $this->organisation->org_id,
            'user_id' => $this->user->id,
        ]);

        // Create categories
        $this->categories = Category::factory()->count(3)->create();

        // Create products
        $this->products = Product::factory()->count(10)->create([
            'org_id' => $this->organisation->org_id
        ]);
    }


    /** @test */
    public function it_returns_products_based_on_search_criteria()
    {
        $category = $this->categories->first()->name;
        $product = $this->products->first();

        $response = $this->getJson("/api/v1/organisations/{$this->organisation->org_id}/products/search?name={$product->name}&category={$category}&minPrice=0&maxPrice=1000");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status_code',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'price',
                    'cost_price',
                    'image',
                    'description',
                    'quantity',
                    'category',
                    'status',
                    'size',
                    'created_at',
                    'updated_at',
                    'deletedAt'
                ]
            ]
        ]);
    }

    /** @test */
    public function it_returns_no_results_when_no_products_match()
    {
        $response = $this->getJson("/api/v1/organisations/{$this->organisation->org_id}/products/search?name=NonexistentProduct");

        $response->assertStatus(200);
        $response->assertJson([
            'status_code' => 200,
            'message' => 'Products retrieved successfully',
            'data' => []
        ]);
    }
}
