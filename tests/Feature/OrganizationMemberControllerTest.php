<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Organisation;
use Illuminate\Support\Facades\Hash;

class OrganizationMemberControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

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
        $response = $this->postJson('/api/v1/organisations', [
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
        $response = $this->getJson("/api/v1/organisations/{$organisation}/members?page=1&page_size=10", [
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
}
// Added comment so i can push again
