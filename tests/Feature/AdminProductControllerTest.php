<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Http\Response;

class AdminProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    public function test_can_list_products()
    {
        Product::factory()->count(5)->create();
        $response = $this->actingAs($this->admin, 'api')->getJson('/api/v1/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['name', 'price', 'description', 'imageUrl', 'quantity', 'status', 'user_id', 'slug', 'tags']
                ],
                'links',
                'meta',
            ]);
    }

public function test_can_show_product()
{
    $product = Product::factory()->create();
    $response = $this->actingAs($this->admin, 'api')->getJson("/api/v1/products/{$product->id}");

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                [
                    'user_id' => $product->user_id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'price' => number_format($product->price, 2, '.', ''),
                    'slug' => $product->slug,
                    'tags' => $product->tags,
                    'imageUrl' => $product->imageUrl,
                    'status' => $product->status,
                    'quantity' => (string)$product->quantity,
                    'is_archived' => false,
                    'created_at' => $product->created_at->toISOString(),
                    'updated_at' => $product->updated_at->toISOString(),
                ]
            ]
        ]);
}


public function test_can_update_product()
{
    $product = Product::factory()->create();
    $updatedData = [
        'name' => 'Updated Product Name',
        'description' => 'Updated description.',
        'price' => 99.99,
        'imageUrl' => 'https://example.com/new-image.jpg',
        'quantity' => 10,
        'status' => 'active',
        'slug' => 'updated-product-name',
        'tags' => 'new,product',
    ];

    $response = $this->actingAs($this->admin, 'api')
    ->patchJson("/api/v1/products/{$product->product_id}", $updatedData);

$response->assertStatus(200)
    ->assertJsonStructure([
        'message',
        'data' => [
            'product_id', 'user_id', 'name', 'description', 'price', 'imageUrl',
        ]
    ]);

    $this->assertDatabaseHas('products', [
        'product_id' => $product->product_id,
        'name' => 'Updated Product Name',
    ]);
}

    public function test_can_delete_product()
    {
        $product = Product::factory()->create();


    $response = $this->actingAs($this->admin, 'api')
                     ->deleteJson("/api/v1/products/{$product->product_id}");


    $response->assertStatus(204);
    $this->assertDatabaseMissing('products', ['product_id' => $product->product_id]);
    }

    public function test_can_edit_product()
    {
        $product = Product::factory()->create();
        $response = $this->actingAs($this->admin, 'api')->getJson("/api/v1/products/{$product->product_id}/edit");

        $response->assertStatus(200)
            ->assertJson([
                'data' => $product->toArray()
            ]);
    }

    public function test_can_get_total_revenue()
    {
        Product::factory()->count(5)->create(['price' => 1000]);
        $response = $this->actingAs($this->admin, 'api')->getJson('/api/v1/products/stats/total-revenue');

        $response->assertStatus(200)
            ->assertJson(['total_revenue' => 50.00]);
    }

    public function test_can_get_total_price()
    {
        Product::factory()->count(5)->create(['price' => 1000]);
        $response = $this->actingAs($this->admin, 'api')->getJson('/api/v1/products/stats/total-price');

        $response->assertStatus(200)
            ->assertJson(['total_price' => 50.00]);
    }

    public function test_unauthorized_user_cannot_access_products()
    {
        $regularUser = User::factory()->create([
            'role' => 'user',
            'is_active' => true,
        ]);

        $response = $this->actingAs($regularUser, 'api')->getJson('/api/v1/products');
        $response->assertStatus(401);
    }


}

