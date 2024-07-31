<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Organisation;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use Tests\TestCase;

class ShowRoleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test fetching a role successfully.
     *
     * @return void
     */
    public function testFetchRoleSuccessfully()
    {
        $organisation = Organisation::factory()->create();
        $role = Role::factory()->create(['org_id' => $organisation->id]);
        $permissions = Permission::factory()->count(3)->create();

        // Attach permissions to role
        $role->permissions()->attach($permissions);

        $response = $this->getJson("/api/v1/organisations/{$organisation->id}/roles/{$role->id}");

        $permissionsArray = $permissions->map(function ($permission) use ($role) {
            return [
                'id' => $permission->id,
                'category' => $permission->category,
                'permission_list' => [
                    'can_view_transactions' => $role->permissions()->where('permission_id', $permission->id)->where('name', 'can_view_transactions')->exists(),
                    'can_view_refunds' => $role->permissions()->where('permission_id', $permission->id)->where('name', 'can_view_refunds')->exists(),
                    'can_edit_transactions' => $role->permissions()->where('permission_id', $permission->id)->where('name', 'can_edit_transactions')->exists(),
                ],
            ];
        })->toArray();

        $response->assertStatus(200)
                 ->assertJson([
                     'status_code' => 200,
                     'data' => [
                         'id' => $role->id,
                         'name' => $role->name,
                         'description' => $role->description,
                         'permissions' => $permissionsArray,
                     ],
                 ]);
    }

    /**
     * Test fetching a role with invalid organisation ID format.
     *
     * @return void
     */
    public function testFetchRoleWithInvalidOrganisationIdFormat()
    {
        $response = $this->getJson('/api/v1/organisations/invalid_id/roles/1');

        $response->assertStatus(400)
                 ->assertJson([
                     'status_code' => 400,
                     'error' => 'Bad Request',
                     'message' => 'Invalid organisation ID or role ID format',
                 ]);
    }

    /**
     * Test fetching a role with invalid role ID format.
     *
     * @return void
     */
    public function testFetchRoleWithInvalidRoleIdFormat()
    {
        $organisation = Organisation::factory()->create();

        $response = $this->getJson("/api/v1/organisations/{$organisation->id}/roles/invalid_id");

        $response->assertStatus(400)
                 ->assertJson([
                     'status_code' => 400,
                     'error' => 'Bad Request',
                     'message' => 'Invalid organisation ID or role ID format',
                 ]);
    }

    /**
     * Test fetching a role for a non-existent organisation.
     *
     * @return void
     */
    public function testFetchRoleForNonExistentOrganisation()
    {
        $response = $this->getJson('/api/v1/organisations/999/roles/1');

        $response->assertStatus(404)
                 ->assertJson([
                     'status_code' => 404,
                     'error' => 'Not Found',
                     'message' => 'The organisation with ID 999 does not exist',
                 ]);
    }

    /**
     * Test fetching a non-existent role.
     *
     * @return void
     */
    public function testFetchNonExistentRole()
    {
        $organisation = Organisation::factory()->create();

        $response = $this->getJson("/api/v1/organisations/{$organisation->id}/roles/999");

        $response->assertStatus(404)
                 ->assertJson([
                     'status_code' => 404,
                     'error' => 'Not Found',
                     'message' => 'The role with ID 999 does not exist',
                 ]);
    }
}
