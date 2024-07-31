<?php

namespace Tests\Feature;

use App\Models\Organisation;
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

        // Create a product with variants
        $product = Product::factory()->create();
        $size = Size::create(['size' => 'standard']);
        $existingVariant = ProductVariant::factory()->create(['product_id' => $product->product_id, 'size_id' => $size->id]);

        $newSizeId = Size::create(['size' => 'large'])->id;
        // Mock the request data
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

        // Mock the UpdateProductRequest
        // $request = Mockery::mock(UpdateProductRequest::class);
        // $request->shouldReceive('validated')->andReturn($data);
        // $request->shouldReceive('input')->with('productsVariant')->andReturn($data['productsVariant']);

        // Call the update method
        // $controller = new ProductController();
        // $response = $controller->update($request, $product->product_id);

        // $response = $this->patchJson(route('products.update', $product->product_id), $data);
        $organisation = Organisation::factory()->create();

        $response = $this->patchJson("/api/v1/products/{$product->product_id}", $data);
// dd($response);
        // Assert the response
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
        $newVariant = ProductVariant::where('product_id', $product->product_id)->where('size_id', 'new_size_id')->first();
        $this->assertDatabaseHas('product_variant_sizes', [
            'product_variant_id' => $newVariant->id,
            'size_id' => $newSizeId
        ]);
    }
}
