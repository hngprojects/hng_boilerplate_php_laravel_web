<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Product;

class SuperAdminProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testSuperAdminCanCreateAndUpdateProduct()
    {
        $this->artisan('migrate:fresh --seed');

        // Log in as Super Admin
        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'email' => 'bulldozeradmin@hng.com',
            'password' => 'bulldozer',
        ]);

        $loginResponse->assertStatus(200);
        $loginResponse->assertJsonStructure([
            'status_code',
            'message',
            'access_token',
            'data' => [
                'user' => [
                    'id',
                    'email',
                    'role',
                ],
            ],
        ]);

        $accessToken = $loginResponse->json('access_token');
        $userId = $loginResponse->json('data.user.id');

        // Fetch a valid organization ID from the seeded data
        $validOrgId = Product::first()->org_id;

        // Create a product
        $productResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ])->postJson('/api/v1/products', [
            'name' => 'okoz',
            'description' => 'boy',
            'price' => 10,
            'status' => 'active',
            'slug' => 'jkdffjk',
            'tags' => 'gk;fk',
            'quantity' => '5',
            'org_id' => $validOrgId,
        ]);

        // Assert product creation was successful
        $productResponse->assertStatus(201);
        $productResponse->assertJson([
            'success' => true,
            'status_code' => 201,
            'message' => 'Product created successfully',
            'data' => [
                'name' => 'okoz',
                'description' => 'boy',
                'price' => 10,
                'status' => 'active',
                'slug' => 'jkdffjk',
                'tags' => 'gk;fk',
                'quantity' => '5',
                'org_id' => $validOrgId,
                'is_archived' => false,
                'imageUrl' => null,
                'user_id' => $userId,
            ],
        ]);

        $productId = $productResponse->json('data.product_id');

        $updateData = [
            'name' => 'Updated Name',
            'description' => 'Updated Description',
            'price' => 200,
            'status' => 'active',
            'quantity' => 100,
        ];

        $updateResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ])->patchJson("/api/v1/products/{$productId}", $updateData);


        $updateResponse->assertStatus(200);
        $updateResponse->assertJson([
            'success' => true,
            'status_code' => 200,
            'message' => 'Product updated successfully',
            'data' => [
                'name' => 'Updated Name',
                'description' => 'Updated Description',
                'price' => 200,
                'status' => 'active',
                'quantity' => 100,
            ],
        ]);
    }
}
