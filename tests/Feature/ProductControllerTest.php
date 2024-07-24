<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Http\Controllers\Api\ProductController;


class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testCategoriesEndpoint()
    {
        // Create a user
        $user = User::factory()->create();

        // Act as the created user
        $response = $this->actingAs($user, 'api')->get('/api/v1/products/categories');

        // Check the response status
        $response->assertStatus(200);

        // Check the JSON structure
        $response->assertJsonStructure([
            'status_code',
            'categories' => [
                '*' => [
                    'id', 'name', 'description', 'slug', 'parent_id'
                ]
            ]
        ]);
    }
}
