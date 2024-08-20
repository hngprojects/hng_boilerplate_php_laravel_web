<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Product;
use App\Models\User;
use App\Models\Organisation;
use Faker\Factory as Faker;

class SuperAdminProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testSuperAdminCanCreateUpdateAndDeleteProduct()
    {
        $faker = Faker::create();
        $uniqueEmail = $faker->unique()->safeEmail;

        $this->artisan('migrate:fresh --seed');

        // Create a user with a unique email
        $user = User::factory()->create([
            'email' => $uniqueEmail,
            'role' => 'admin',
        ]);

        $organisation = Organisation::factory()->create([
            'user_id' => $user->id,
        ]);

        // Fetch the `org_id` from an existing product or create a dummy product
        $product = Product::factory()->create([
            'user_id' => $user->id,
            'org_id' => $organisation->id,
        ]);

        $validOrgId = $product->org_id;

        // Simulate product creation
        $productData = [
            'name' => 'newer',
            'description' => 'boys',
            'price' => 10,
            'quantity' => 5,
            'org_id' => $validOrgId,
        ];

        $productResponse = $this->postJson('/api/v1/products', $productData);

        $productResponse->assertStatus(201);
        $productResponse->assertJson([
            'success' => true,
            'status_code' => 201,
            'message' => 'Product created successfully',
            'data' => array_merge($productData, [
                'slug' => 'newer-otxwfwgm',
                'tags' => 'default-tag',
                'imageUrl' => null,
                'is_archived' => false,
                'product_id' => $productResponse->json('data.product_id'),
                'created_at' => $productResponse->json('data.created_at'),
                'updated_at' => $productResponse->json('data.updated_at'),
            ]),
        ]);

        $productId = $productResponse->json('data.product_id');

        // Simulate product update
        $updateData = [
            'name' => 'new update',
            'description' => 'new description',
        ];

        $updateResponse = $this->patchJson("/api/v1/products/{$productId}", $updateData);

        $updateResponse->assertStatus(200);
        $updateResponse->assertJson([
            'success' => true,
            'status_code' => 200,
            'message' => 'Product updated successfully',
            'data' => array_merge($updateData, [
                'product_id' => $productId,
                'user_id' => $user->id,
                'price' => 10,
                'slug' => 'newer-otxwfwgm',
                'tags' => 'default-tag',
                'imageUrl' => null,
                'status' => 'active',
                'quantity' => 5,
                'is_archived' => false,
                'org_id' => $validOrgId,
                'created_at' => $productResponse->json('data.created_at'),
                'updated_at' => now()->toISOString(),
                'category' => null,
            ]),
        ]);

        // Simulate product deletion
        $deleteResponse = $this->deleteJson("/api/v1/products/{$productId}");

        $deleteResponse->assertStatus(200);
        $deleteResponse->assertJson([
            'success' => true,
            'status_code' => 200,
            'message' => 'Product deleted successfully',
        ]);

        // Ensure the product is no longer in the database
        $this->assertDatabaseMissing('products', ['id' => $productId]);
    }
}
