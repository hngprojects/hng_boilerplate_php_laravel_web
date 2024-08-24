<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\Organisation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrganisationRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_role_by_org_and_role_id()
    {
          
        $organisation = Organisation::factory()->create();
        $user = User::factory()->create();
        $organisation->users()->attach($user->id);

        $role = Role::create([
            'name' => 'Craft Artist',
            'description' => 'Quae voluptas fuga animi expedita natus qui.',
            'org_id' => $organisation->org_id,
            'is_active' => true,
            'is_default' => false,
        ]);

        // Generate a JWT token
        $token = JWTAuth::fromUser($user);

        // Send request with the JWT token
        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->getJson("/api/v1/organisation/{$organisation->org_id}/roles/{$role->id}");

        // Assert success response
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Role retrieved successfully',
                'data' => [
                    'role_id' => $role->id,
                    'name' => $role->name,
                    'description' => $role->description,
                    'org_id' => $role->org_id,
                    'is_active' => $role->is_active,
                    'is_default' => $role->is_default,
                ]
            ]);

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'org_id' => $organisation->org_id,
        ]);
    }

    public function test_get_role_by_org_and_role_id_forbidden()
    {
        $user = User::factory()->create();
        $organisation = Organisation::factory()->create();
        $organisation->users()->attach($user->id);

        $otherUser = User::factory()->create();
        $token = JWTAuth::fromUser($otherUser);

        $role = Role::factory()->create([
            'org_id' => $organisation->org_id
        ]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->getJson("/api/v1/organisation/{$organisation->org_id}/roles/{$role->id}");

        // Assert forbidden response
        $response->assertStatus(403)
            ->assertJson([
                'status' => 'forbidden',
                'message' => 'User does not have permission to access this role',
            ]);
    }

    public function test_get_role_by_org_and_role_id_not_found()
    {
        $user = User::factory()->create();
        $organisation = Organisation::factory()->create();
        $organisation->users()->attach($user->id);

        // Generate a JWT token
        $token = JWTAuth::fromUser($user);

        // Send request for a non-existent role
        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->getJson("/api/v1/organisation/{$organisation->org_id}/roles/999");

        // Assert not found response
        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'Role not found or does not belong to the specified organisation',
            ]);
    }
}
