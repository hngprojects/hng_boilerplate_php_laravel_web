<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Organisation;
use App\Models\OrganisationUser;
use App\Models\Product;
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
            'name' => 'Test Product',
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 100,
            'status' => 'in stock',
            'image_url' => $image,
            'category' => 'Electronics',
            'quantity' => '11',
        ];

        // Send POST request to create product
        $response = $this->postJson("/api/v1/organisations/{$organisation->org_id}/products", $payload);
        $response = $this->postJson("/api/v1/organisations/{$organisation->org_id}/products", $payload);

        // Assert the response status and structure
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'status_code',
                'data' => [
                    'name',
                    'description',
                    'price',
                    'status',
                    'updated_at',
                    'created_at',
                ]
            ]);

        // Assert the product is created in the database
        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 100,
            'user_id' => $user->id
        ]);
    }

    /** @test */
    public function it_cannot_create_a_product_if_not_an_owner()
    {
        // Create two users, one who is not an owner
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $this->actingAs($otherUser);




        // Create an organisation and get the first instance
        $organisation = Organisation::factory()->create();

        // Define the payload with an image file
        Storage::fake('public');
        $image = UploadedFile::fake()->image('test_image.jpg');

        $payload = [
            'name' => 'Unauthorized Product',
            'name' => 'Unauthorized Product',
            'description' => 'Unauthorized Description',
            'price' => 100,
            'image_url' => $image,
            'category' => 'Beans',
            'quantity' => '7',
            'status' => 'in stock',

        ];

        // Send POST request to create product
        $response = $this->postJson("/api/v1/organisations/{$organisation->org_id}/products", $payload);
        $response = $this->postJson("/api/v1/organisations/{$organisation->org_id}/products", $payload);

        // Assert the response status is 403 Forbidden
        $response->assertStatus(403)
            ->assertJson(['message' => 'You are not authorized to create products for this organisation.']);
    }
}
