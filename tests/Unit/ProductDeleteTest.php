<?php

namespace Tests\Feature;

use App\Models\CategoryProduct;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Product;
use App\Models\Organisation;
use App\Models\OrganisationUser;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProductDeleteTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $otherUser;
    protected $org;
    protected $otherOrg;
    protected $product;
    /**
     * @test
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create users
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();

        // Create organizations
        $this->org = Organisation::create([
            'name' => 'Bamo',
            'description' => 'Bamo\'s Organisation',
            'email' => 'bamo8@gmail.com',
            'industry' => 'Tech',
            'type' => 'fulltime',
            'country' => 'Nigeria',
            'address' => 'Tanke, Ilorin',
            'state' => 'Kwara',
            'user_id' => $this->user->id,

        ]);

        $this->otherOrg = Organisation::create([
            'name' => 'Other Org',
            'description' => 'Another organization',
            'email' => 'other@example.com',
            'industry' => 'Finance',
            'type' => 'parttime',
            'country' => 'Nigeria',
            'address' => 'Other address',
            'state' => 'Lagos',
            'user_id' => $this->otherUser->id,

        ]);
        // Assign users as owners of the organizations
        $this->org->users()->attach($this->user->id);
        $this->otherOrg->users()->attach($this->otherUser->id);

        $this->product = Product::create([
            'name' => 'Test',
            'description' => 'Testing',
            'slug' => Carbon::now(),
            'category' => 'food',
            'tags' => 'Food',
            'price' => 100,
            'imageUrl' => 'https://via.placeholder.com/640x480.png/0099ff?text=fuga',
            'org_id' => $this->org->org_id,
            'user_id' => $this->user->id,
            'status' => 'active',
            'quantity' => 80
        ]);
    }
    /**
     * @test
     */
    public function testUserCanDeleteProduct()
    {

        $token = JWTAuth::fromUser($this->user);
        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->deleteJson("api/v1/organizations/{$this->org->org_id}/products/{$this->product->product_id}");


        $response->assertStatus(204);
        $this->assertDatabaseMissing('products', ['product_id' => $this->product->product_id]);
    }
    /**
     * @test
     */
    public function testUserCannotDeleteProductIfNotOwner()
    {

        $token = JWTAuth::fromUser($this->otherUser);
        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->deleteJson("api/v1/organizations/{$this->org->org_id}/products/{$this->product->product_id}");

        $response->assertStatus(403);
        $response->assertJson([
            'status' => 'Forbidden',
            'message' => 'You do not have permission to delete a product from this organization.',
            'status_code' => 403
        ]);
    }
}
