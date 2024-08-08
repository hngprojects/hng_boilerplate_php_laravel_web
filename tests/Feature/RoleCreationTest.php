<?php

namespace Tests\Feature;

use App\Models\Organisation;
use App\Models\Permission;
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
        ]);

        $this->assertDatabaseHas('users', ['name' => $this->test_user->name]);
        $this->assertDatabaseHas('organisations', ['name' => $this->test_org->name]);
        $this->assertDatabaseHas('permissions', ['name' => 'test permission 1']);
    }

    public function test_role_creation_is_successful()
    {
        // Authenticate the test user
        $this->actingAs($this->test_user, 'api');

        $response = $this->postJson('/api/v1/organisations/' . $this->test_org->org_id . '/roles', [
            'role_name' => 'Test role',
            'organisation_id' => $this->test_org->org_id,
            'permissions_id' => $this->test_permission->id,
        ]);

        // Print the response content to see the validation errors
        $response->dump();

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'status_code',
                     'message'
                 ]);

        $this->assertDatabaseHas('roles', [
            'name' => 'Test role',
        ])->assertDatabaseCount('roles_permissions', 1);
    }
}
