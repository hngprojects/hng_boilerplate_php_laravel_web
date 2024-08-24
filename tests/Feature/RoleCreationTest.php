<?php
namespace Tests\Feature;

use App\Models\Organisation;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleCreationTest extends TestCase
{
    use RefreshDatabase;

    protected User $test_user;
    protected Organisation $test_org;
    protected Permission $test_permission;

    protected function setUp(): void
    {
        parent::setUp();

        $this->test_user = User::factory()->create();

        $this->test_org = Organisation::factory()->create([
            'user_id' => $this->test_user->id,
        ]);

        $this->test_permission = Permission::create([
            'name' => 'test permission 1',
            'description' => 'Test permission description',
        ]);

        // Authenticate the test user
        $this->actingAs($this->test_user, 'api');

        // Ensure test data exists in the database
        $this->assertDatabaseHas('users', ['name' => $this->test_user->name]);
        $this->assertDatabaseHas('organisations', ['name' => $this->test_org->name]);
        $this->assertDatabaseHas('permissions', ['name' => $this->test_permission->name]);
    }

    public function test_role_creation_is_successful()
    {

        // Send the role creation request
        $response = $this->postJson('/api/v1/organisations/' . $this->test_org->org_id . '/roles', [
            'name' => 'Test role',
            'description' => 'Test role description',
            'permissions' => [$this->test_permission->id],
        ]);

        // Decode the response to get the role ID
        $responseData = $response->json();
        $createdRoleId = $responseData['data']['id'];


        // Assert that the response status is 201 (Created) and check the JSON structure
        $response->assertStatus(201)
            ->assertJson([
                'status_code' => 201,
                'data' => [
                    'id' => $createdRoleId,  // Check that an ID is returned
                    'name' => 'Test role',
                    'description' => 'Test role description',
                    'permissions' => [
                        [
                            'id' => $this->test_permission->permission_id,
                            'name' => $this->test_permission->name,
                        ],
                    ],
                ],
                'error' => null,
                'message' => 'Role created successfully',
            ]);

        // Assert that the role is in the database with the correct name and description
        $this->assertDatabaseHas('roles', [
            'name' => 'Test role',
            'description' => 'Test role description',
        ]);

        // Assert that the role has been correctly associated with the permission
        $this->assertDatabaseCount('roles_permissions', 1);
    }

}
