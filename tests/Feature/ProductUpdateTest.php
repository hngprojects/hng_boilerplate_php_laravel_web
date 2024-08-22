<?php

namespace Tests\Feature;

use App\Models\Organisation;
use App\Models\OrganisationUser;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_update_a_product()
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create an organisation and get the first instance
        $organisation = Organisation::factory()->create();

        // Associate the user with the organization as an owner
        OrganisationUser::create([
            'org_id' => $organisation->org_id,
            'user_id' => $user->id,
        ]);

        // Create a product to update
        $product = Product::factory()->create([
            'org_id' => $organisation->org_id,
            'user_id' => $user->id,
        ]);

        $data = [
            'name' => 'Updated Product',
            'quantity' => 20,
            'price' => 120,
            'category' => 'Updated Category',
            'description' => 'Updated Description',
        ];

        $response = $this->patchJson("/api/v1/organisations/{$organisation->org_id}/products/{$product->product_id}", $data);

        // Assert the response status and structure
        $response->assertStatus(200)
            ->assertJson([
                'status_code' => 200,
                'message' => 'Products updated successfully',
                'data' => [
                    'name' => 'Updated Product',
                    'price' => 120,
                    'description' => 'Updated Description',
                    'quantity' => 20,
                    'status' => $product->status,
                    'category' => 'Updated Category',
                    'id' => $product->product_id,
                ],
            ]);

        // Assert the product is updated in the database
        $this->assertDatabaseHas('products', [
            'product_id' => $product->product_id,
            'name' => 'Updated Product',
            'quantity' => 20,
            'price' => 120,
            'category' => 'Updated Category',
            'description' => 'Updated Description',
        ]);
    }

    /** @test */
    public function it_cannot_update_a_product_if_not_an_owner()
    {
        // Create two users, one who is not an owner
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $this->actingAs($otherUser);

        // Create an organisation and get the first instance
        $organisation = Organisation::factory()->create();

        // Create a product
        $product = Product::factory()->create([
            'org_id' => $organisation->org_id,
            'user_id' => $user->id,
        ]);

        $data = [
            'name' => 'Unauthorized Update',
            'description' => 'Unauthorized Description',
            'price' => 150,
            'quantity' => 10,
            'category' => 'Unauthorized Category',
        ];

        // Send PATCH request to update product
        $response = $this->patchJson("/api/v1/organisations/{$organisation->org_id}/products/{$product->product_id}", $data);

        // Assert the response status is 403 Forbidden
        $response->assertStatus(403)
            ->assertJson(['message' => 'You are not authorized to update products for this organisation.']);
    }
}
