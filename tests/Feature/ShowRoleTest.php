<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use App\Models\Organisation;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

class ShowRoleTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'api'); // Ensure user is authenticated
    }

    /**
     * Test fetching a role successfully.
     *
     * @return void
     */
    public function testFetchRoleSuccessfully()
    {
        $organisation = Organisation::factory()->create(['user_id' => $this->user->id]);
        $role = Role::factory()->create(['org_id' => $organisation->org_id]);
        $permissions = Permission::factory()->count(3)->create();

        // Attach permissions to role
        $role->permissions()->attach($permissions->pluck('id'));

        $response = $this->getJson("/api/v1/organisations/{$organisation->org_id}/roles/{$role->id}");

        $permissionsArray = $permissions->map(function ($permission) use ($role) {
            return [
                'id' => $permission->id,
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
        // Use an invalid UUID format
        $response = $this->getJson('/api/v1/organisations/invalid_uuid_format/roles/1');

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
        $organisation = Organisation::factory()->create(['user_id' => $this->user->id]);

        // Use a valid UUID for organisation but invalid format for role ID
        $response = $this->getJson("/api/v1/organisations/{$organisation->org_id}/roles/invalid_uuid_format");

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
        // Use a UUID that does not exist in the database
        $response = $this->getJson('/api/v1/organisations/00000000-0000-0000-0000-000000000000/roles/1');

        $response->assertStatus(404)
                 ->assertJson([
                     'status_code' => 404,
                     'error' => 'Not Found',
                     'message' => 'The organisation with ID 00000000-0000-0000-0000-000000000000 does not exist',
                 ]);
    }

    /**
     * Test fetching a non-existent role.
     *
     * @return void
     */
    public function testFetchNonExistentRole()
    {
        $organisation = Organisation::factory()->create(['user_id' => $this->user->id]);

        // Use a valid UUID for organisation but one that does not exist as a role
        $response = $this->getJson("/api/v1/organisations/{$organisation->org_id}/roles/00000000000000000000000000000000");

        $response->assertStatus(404)
                 ->assertJson([
                     'status_code' => 404,
                     'error' => 'Not Found',
                     'message' => 'The role with ID 00000000000000000000000000000000 does not exist',
                 ]);
    }
}
