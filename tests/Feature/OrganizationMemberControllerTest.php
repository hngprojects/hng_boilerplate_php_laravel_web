<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Organisation;
use Illuminate\Support\Facades\Hash;

class OrganizationMemberRetrievalTest extends TestCase
{
    use LazilyRefreshDatabase;

    /** @test */
    public function it_validates_the_organization_id_and_returns_paginated_members()
    {
        // Create a user
        $user = User::factory()->create([
            'password' => Hash::make('password123')
        ]);

        // Login the user
        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password123'
        ]);

        $response->assertStatus(200);
        $token = $response->json('access_token');

        // Create an organization
        $organisation = Organisation::factory()->create();

        // Fetch members with valid organization ID
        $response = $this->getJson("/api/v1/organization/{$organisation->id}/members?page=1&page_size=10", [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'current_page',
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'email'
                        ]
                    ],
                    'first_page_url',
                    'last_page',
                    'last_page_url',
                    'next_page_url',
                    'path',
                    'per_page',
                    'prev_page_url',
                    'to',
                    'total'
                ]
            ]);
    }

    /** @test */
    public function it_returns_404_for_invalid_organization_id()
    {
        // Create a user
        $user = User::factory()->create([
            'password' => Hash::make('password123')
        ]);

        // Login the user
        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password123'
        ]);

        $response->assertStatus(200);
        $token = $response->json('access_token');

        // Fetch members with an invalid organization ID
        $response = $this->getJson('/api/v1/organization/invalid-id/members?page=1&page_size=10', [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'fail',
                'message' => 'Organization not found'
            ]);
    }

    /** @test */
    public function it_returns_unauthorized_error_when_user_is_not_authenticated()
    {
        // Create an organization
        $organisation = Organisation::factory()->create();

        // Fetch members without authentication
        $response = $this->getJson("/api/v1/organization/{$organisation->id}/members?page=1&page_size=10");

        $response->assertStatus(401)
            ->assertJson([
                'status' => 'fail',
                'message' => 'Unauthorized'
            ]);
    }

    /** @test */
    public function it_returns_empty_list_if_no_members_exist_for_valid_organization()
    {
        // Create a user
        $user = User::factory()->create([
            'password' => Hash::make('password123')
        ]);

        // Login the user
        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password123'
        ]);

        $response->assertStatus(200);
        $token = $response->json('access_token');

        // Create an organization without members
        $organisation = Organisation::factory()->create();

        // Fetch members
        $response = $this->getJson("/api/v1/organization/{$organisation->id}/members?page=1&page_size=10", [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Organization members fetched successfully',
                'data' => [
                    'data' => [],
                    'total' => 0
                ]
            ]);
    }

    /** @test */
    public function it_supports_pagination_for_member_list()
    {
        // Create a user
        $user = User::factory()->create([
            'password' => Hash::make('password123')
        ]);

        // Login the user
        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password123'
        ]);

        $response->assertStatus(200);
        $token = $response->json('access_token');

        // Create an organization
        $organisation = Organisation::factory()->create();

        // Add multiple members to the organization
        // Assuming there is a Member model related to Organisation
        $organisation->members()->createMany([
            ['name' => 'Member 1', 'email' => 'member1@example.com'],
            ['name' => 'Member 2', 'email' => 'member2@example.com'],
            // Add more members as needed
        ]);

        // Fetch members with pagination
        $response = $this->getJson("/api/v1/organization/{$organisation->id}/members?page=1&page_size=1", [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Organization members fetched successfully',
                'data' => [
                    'current_page' => 1,
                    'per_page' => 1,
                    'total' => 2 // Adjust as per the number of members added
                ]
            ]);
    }
}
