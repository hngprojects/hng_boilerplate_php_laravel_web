<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Product;
use App\Models\User;

class ProductIndexTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        // Seed the database with test data
        Product::factory()->count(50)->create();
    }

    /** @test */
    public function it_fetches_products_with_pagination()
    {
        // Create and authenticate a user
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $response = $this->getJson('/api/v1/products?page=1&limit=10');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'products' => [
                '*' => ['name', 'price']
            ],
            'pagination' => [
                'totalItems',
                'totalPages',
                'currentPage'
            ],
            'status_code'
        ]);
    }

    /** @test */
    public function it_validates_pagination_parameters()
    {
        // Create and authenticate a user
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $response = $this->getJson('/api/v1/products?page=0&limit=10');

        $response->assertStatus(400);
        $response->assertJson([
            'status' => 'bad request',
            'message' => 'Invalid query params passed',
            'status_code' => 400
        ]);
    }

    /** @test */
    // public function it_handles_internal_server_error()
    // {
    //     // Create and authenticate a user
    //     $user = User::factory()->create();
    //     $this->actingAs($user, 'api');

    //     // Mock the Product model to throw an exception
    //     $this->mock(Product::class, function ($mock) {
    //         $mock->shouldReceive('count')->andThrow(new \Exception('Internal server error'));
    //     });

    //     $response = $this->getJson('/api/v1/products?page=1&limit=10');

    //     $response->assertStatus(500);
    //     $response->assertJson([
    //         'status' => 'error',
    //         'message' => 'Internal server error',
    //         'status_code' => 500
    //     ]);
    // }
}
