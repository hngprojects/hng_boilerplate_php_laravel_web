<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Organisation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OrganizationMemberControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected $user;
    protected $organization;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user and log them in
        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        // Create an organization
        $this->organization = Organisation::factory()->create();
    }

    /** @test */
    public function it_validates_the_organization_id_and_returns_paginated_members()
    {
        // Create a user
        $user = User::factory()->create([
            'name' => 'precious',
            'email' => 'precious@example.com',
            'password' => Hash::make('precious')
        ]);

        // Login the user
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'precious@example.com',
            'password' => 'precious'
        ]);

        $response->assertStatus(200);
        $token = $response->json('data.access_token');

        // Create an organization
        $response = $this->postJson('/api/v1/organizations', [
            'name' => 'Example Organization',
            'description' => 'This is an example organization description.',
            'email' => 'example@example.com',
            'industry' => 'Technology',
            'type' => 'Non-profit',
            'country' => 'United States',
            'address' => '123 Example St',
            'state' => 'California'
        ], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(201);
        $organisation = $response->json('data.org_id');

        // Fetch members with valid organization ID
        $response = $this->getJson("/api/v1/organizations/{$organisation}/users?page=1&page_size=10", [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'members' => [
                        '*' => [
                            'userId',
                            'firstName',
                            'email',
                            'phone'
                        ]
                    ],
                    'pagination' => [
                        'currentPage',
                        'pageSize',
                        'totalPages',
                        'totalItems'
                    ]
                ],
                'status_code'
            ]);
    }

    public function test_it_returns_users_when_searching_with_valid_organization_id()
    {
        // Create users belonging to the organization
        $user1 = $this->organization->users()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $user2 = $this->organization->users()->create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => bcrypt('password'),
        ]);

        // Search for users in the organization
        $response = $this->getJson('/api/v1/members/' . $this->organization->org_id . '/search?search=Jane');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Users retrieved successfully',
                'status_code' => 200,
                'data' => [
                    [
                        'name' => 'Jane Smith',
                        'email' => 'jane@example.com',
                    ]
                ]
            ]);
    }

    public function test_it_returns_an_error_when_organization_id_is_invalid()
    {
        // Pass an invalid organization ID
        $invalidOrgId = Str::random(10);

        $response = $this->getJson('/api/v1/members/' . $invalidOrgId . '/search');

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Invalid organization ID',
                'status_code' => 400,
            ]);
    }

    public function test_it_returns_an_error_when_organization_does_not_exist()
    {
        // Create a non-existing organization ID
        $nonExistingOrgId = Str::uuid()->toString();

        $response = $this->getJson('/api/v1/members/' . $nonExistingOrgId . '/search');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Organization does not exist',
                'status_code' => 404,
            ]);
    }
}
// Added comment so i can push again
