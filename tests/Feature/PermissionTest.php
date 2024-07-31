<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Organisation;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test updating the role name.
     *
     * @return void
     */
    public function test_update_role_name()
    {
        $user = User::factory()->create();
        $organisation = Organisation::factory()->create();
        $role = Role::factory()->create(['org_id' => $organisation->org_id]);
        $organisation->users()->attach($user->id);
        $this->actingAs($user, 'api');

        $newRoleData = [
            'name' => 'Updated Role Name',
            'description' => $role->description,
        ];

        $response = $this->json('PUT', "/api/v1/organisations/{$organisation->org_id}/roles/{$role->id}", $newRoleData);
        $response->assertStatus(200);

        // Assert the response content
        $response->assertJsonFragment([
            'name' => 'Updated Role Name',
        ]);
    }


    public function it_assigns_permissions_successfully()
    {
        $user = User::factory()->create();
        $organisation = Organisation::factory()->create();
        $role = Role::factory()->create(['org_id' => $organisation->org_id]);
        $permissions = Permission::factory()->count(3)->create();
        $permissionList = [];
        foreach ($permissions as $permission) {
          $permissionList[$permission->name] = $role->permissions->contains($permission);
        }

        $data = [
            'permission_list' => $permissionList,
        ];

        $response = $this->actingAs($user)
            ->putJson("/api/v1/roles/{$organisation->org_id}/{$role->id}/permissions", $data);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Permissions updated successfully',
            ]);
    }

    /** @test */
    public function it_returns_error_when_assigning_permissions_fails()
    {
        $user = User::factory()->create();
        $organisation = Organisation::factory()->create();
        $role = Role::factory()->create(['org_id' => $organisation->org_id]);

        // Simulate validation error
        $response = $this->actingAs($user)
            ->putJson("/api/v1/organisations/{$organisation->org_id}/{$role->id}/permissions", []);
        $response->assertStatus(422);

        // Simulate role not found
        $response = $this->actingAs($user)
            ->putJson("/api/v1/organisations/{$organisation->org_id}/999/permissions", ['permission_list' => ['permission' => true]]);

        $response->assertStatus(404)
          ->assertJsonFragment([
                'message' => 'Role not found',
            ]);
    }
}