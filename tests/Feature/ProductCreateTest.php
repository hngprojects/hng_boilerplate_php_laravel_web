<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Organisation;
use App\Models\OrganisationUser;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Size;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductCreateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_product()
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create a size with 'standard'
        $size = Size::create(['size' => 'standard']);

        // Create a category
        $category = Category::create(['name' => 'Test Category', 'slug' => 'test-category', 'description' => 'Testing']);

        // Create an organisation and get the first instance
        $organisation = Organisation::factory()->create();

        // Associate the user with the organisation as an owner
        OrganisationUser::create([
            'org_id' => $organisation->org_id,
            'user_id' => $user->id,
        ]);

        // Define the payload with an image file
        Storage::fake('public');
        $image = UploadedFile::fake()->image('test_image.jpg');

        $payload = [
            'title' => 'Test Product',
            'description' => 'Test Description',
            'category' => $category->id, // Use the created category ID
            'price' => 100,
            'stock' => 10,
            'image' => $image,
        ];

        // Send POST request to create product
        $response = $this->postJson("/api/v1/organisations/{$organisation->org_id}/products", $payload);

        // Assert the response status and structure
        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'product' => [
                    'name',
                    'description',
                    'slug',
                    'tags',
                    'price',
                    'imageUrl',
                    'user_id',
                    'updated_at',
                    'created_at',
                ]
            ]);

        // Assert the product is created in the database
        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'tags' => $category->id,
            'price' => 100,
            'user_id' => $user->id
        ]);

        // Assert the category_product relationship is created
        $this->assertDatabaseHas('category_product', [
            'category_id' => $category->id,
            'product_id' => Product::first()->product_id
        ]);

        // Assert the product variant is created with the correct size
        $this->assertDatabaseHas('product_variants', [
            'product_id' => Product::first()->product_id,
            'stock' => 10,
            'stock_status' => 'in_stock',
            'price' => 100,
            'size_id' => $size->id
        ]);

        // Assert the product variant size is created
        $this->assertDatabaseHas('product_variant_sizes', [
            'product_variant_id' => ProductVariant::first()->id,
            'size_id' => $size->id
        ]);
    }

    /** @test */
    public function it_cannot_create_a_product_if_not_an_owner()
    {
        // Create two users, one who is not an owner
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $this->actingAs($otherUser);

        // Create a size with 'standard'
        $size = Size::create(['size' => 'standard']);

        // Create a category
        $category = Category::create(['name' => 'Test Category', 'slug' => 'test-category', 'description' => 'Testing']);

        // Create an organisation and get the first instance
        $organisation = Organisation::factory()->create();

        // Define the payload with an image file
        Storage::fake('public');
        $image = UploadedFile::fake()->image('test_image.jpg');

        $payload = [
            'title' => 'Unauthorized Product',
            'description' => 'Unauthorized Description',
            'category' => $category->id, // Use the created category ID
            'price' => 100,
            'stock' => 10,
            'image' => $image,
        ];

        // Send POST request to create product
        $response = $this->postJson("/api/v1/organisations/{$organisation->org_id}/products", $payload);

        // Assert the response status is 403 Forbidden
        $response->assertStatus(403)
            ->assertJson(['message' => 'You are not authorized to create products for this organisation.']);
    }
}
