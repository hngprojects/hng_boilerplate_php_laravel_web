<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Organisation;
use Tymon\JWTAuth\Facades\JWTAuth;

class CustomerControllerTest extends TestCase
{
    use RefreshDatabase;

    private function getAuthenticatedUser(string $role)
    {
        $user = User::factory()->create([
            'role' => $role,
            'is_active' => true
        ]);

        $token = JWTAuth::fromUser($user);

        return [$user, $token];
    }

    /** @test */
    public function only_admin_can_fetch_customers()
    {
        [$user, $token] = $this->getAuthenticatedUser('admin');

        $customer = User::factory()->create(['role' => 'customer']);
        $organisations = Organisation::factory()->count(3)->create();
        $customer->organisations()->attach($organisations);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/v1/customers?limit=10&page=1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status_code',
                'current_page',
                'total_pages',
                'limit',
                'total_items',
                'data' => [
                    '*' => [
                        'name',
                        'email',
                        'phone',
                        'organisations'
                    ]
                ]
            ]);
    }

    /** @test */
    public function non_admin_cannot_fetch_customers()
    {
        [$user, $token] = $this->getAuthenticatedUser('customer');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/v1/customers?limit=10&page=1');

        $response->assertStatus(401)
            ->assertJson([
                'status_code' => 401,
                'message' => 'Unauthorized',
                'error' => 'Bad Request'
            ]);
    }

    /** @test */
    public function it_requires_limit_and_page_parameters()
    {
        [$user, $token] = $this->getAuthenticatedUser('admin');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->getJson('/api/v1/customers');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['limit', 'page']);
    }

    /** @test */
    public function it_requires_valid_limit_and_page_parameters()
    {
        [$user, $token] = $this->getAuthenticatedUser('admin');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/v1/customers?limit=0&page=0');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['limit', 'page']);
    }

    /** @test */
    public function it_returns_customers_with_organisations()
    {
        [$user, $token] = $this->getAuthenticatedUser('admin');

        $customer = User::factory()->create(['role' => 'customer']);
        $organisations = Organisation::factory()->count(3)->create();
        $customer->organisations()->attach($organisations);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/v1/customers?limit=10&page=1');

        $response->assertStatus(200)
            ->assertJson([
                'status_code' => 200,
                'current_page' => 1,
                'total_pages' => 1,
                'limit' => 10,
                'total_items' => 1,
                'data' => [
                    [
                        // 'first_name' => $customer->first_name,
                        // 'last_name' => $customer->last_name,
                        'name' => $customer->name, 
                        'email' => $customer->email,
                        'phone' => $customer->phone,
                        'organisations' => $customer->organisations->pluck('org_id')->toArray()
                    ]
                ]
            ]);
    }
}
