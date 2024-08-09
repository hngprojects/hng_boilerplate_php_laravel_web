<?php

namespace Tests\Feature;

use App\Models\Organisation;
use App\Models\OrganisationUser;
use App\Models\Size;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantSize;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Requests\UpdateProductRequest;
use Mockery;

class ProductUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_product_and_variants()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create();
        $size = Size::create(['size' => 'standard']);
        $existingVariant = ProductVariant::factory()->create(['product_id' => $product->product_id, 'size_id' => $size->id]);

        $newSizeId = Size::create(['size' => 'large'])->id;

        $data = [
            'name' => 'Updated Product Name',
            'is_archived' => true,
            'image' => 'http://example.com/image.jpg',
            'productsVariant' => [
                [
                    'size_id' => $size->id,
                    'stock' => 20,
                    'price' => 95.00
                ],
                [
                    'size_id' => $newSizeId,
                    'stock' => 30,
                    'price' => 105.00
                ]
            ]
        ];

        $organisation = Organisation::factory()->create();
        OrganisationUser::create(['org_id' => $organisation->org_id, 'user_id' => $user->id]);

        $response = $this->patchJson("/api/v1/organisations/{$organisation->org_id}/products/{$product->product_id}", $data);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Product updated successfully']);

        // Assert the product was updated
        $this->assertDatabaseHas('products', [
            'product_id' => $product->product_id,
            'name' => 'Updated Product Name',
            'is_archived' => true,
            'imageUrl' => 'http://example.com/image.jpg'
        ]);

        // Assert the existing variant was updated
        $this->assertDatabaseHas('product_variants', [
            'id' => $existingVariant->id,
            'product_id' => $product->product_id,
            'size_id' => $size->id,
            'stock' => 20,
            'stock_status' => 'in_stock',
            'price' => 95.00
        ]);

        // Assert the new variant was created
        $this->assertDatabaseHas('product_variants', [
            'product_id' => $product->product_id,
            'size_id' => $newSizeId,
            'stock' => 30,
            'stock_status' => 'in_stock',
            'price' => 105.00
        ]);

        // Assert the ProductVariantSize entry was created for the new variant
        $newVariant = ProductVariant::where('product_id', $product->product_id)->where('size_id', $newSizeId)->first();
        $this->assertDatabaseHas('product_variant_sizes', [
            'product_variant_id' => $newVariant->id,
            'size_id' => $newSizeId
        ]);
    }

    /** @test */
    public function it_cannot_update_a_product_if_not_an_owner()
    {
        // Create two users, one who is not an owner
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $this->actingAs($otherUser);

        // Create a product
        $product = Product::factory()->create();

        $size = Size::create(['size' => 'standard']);

        ProductVariant::factory()->create(['product_id' => $product->product_id, 'size_id' => $size->id]);

        $newSizeId = Size::create(['size' => 'large'])->id;

        $payload = [
            'name' => 'Unauthorized Product Update',
            'is_archived' => true,
            'image' => 'http://example.com/image.jpg',
            'productsVariant' => [
                [
                    'size_id' => $size->id,
                    'stock' => 20,
                    'price' => 95.00
                ],
                [
                    'size_id' => $newSizeId,
                    'stock' => 30,
                    'price' => 105.00
                ]
            ]
        ];

        $organisation = Organisation::factory()->create();
        OrganisationUser::create(['org_id' => $organisation->org_id, 'user_id' => $user->id]);

        $response = $this->patchJson("/api/v1/organisations/{$organisation->org_id}/products/{$product->product_id}", $payload);

        // Assert the response status is 403 Forbidden
        $response->assertStatus(403)
            ->assertJson(['message' => 'You are not authorized to update products for this organisation.']);

        // Assert the product was not updated
        $this->assertDatabaseMissing('products', [
            'product_id' => $product->product_id,
            'name' => 'Unauthorized Product Update',
            'is_archived' => true,
            'imageUrl' => 'http://example.com/image.jpg'
        ]);

        // Assert the existing variant was not updated
        $this->assertDatabaseMissing('product_variants', [
            'product_id' => $product->product_id,
            'size_id' => $size->id,
            'stock' => 20,
            'stock_status' => 'in_stock',
            'price' => 95.00
        ]);

        // Assert the new variant was not created
        $this->assertDatabaseMissing('product_variants', [
            'product_id' => $product->product_id,
            'size_id' => $newSizeId,
            'stock' => 30,
            'stock_status' => 'in_stock',
            'price' => 105.00
        ]);
    }

}
