<?php

namespace Tests\Feature;

use App\Models\Organisation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleDeletionTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin_user;
    protected Organisation $test_org;
    protected Role $test_role;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create an admin user
        $this->admin_user = User::factory()->create();

        // Create an organisation
        $this->test_org = Organisation::factory()->create([
            'user_id' => $this->admin_user->id,
        ]);

        // Create a role within the organisation
        $this->test_role = Role::create([
            'name' => 'Test Role',
            'org_id' => $this->test_org->org_id,
            'description' => 'Test role description',
            'is_active' => true,
            'is_default' => false,
        ]);

        // Verify the initial database state
        $this->assertDatabaseHas('users', ['id' => $this->admin_user->id]);
        $this->assertDatabaseHas('organisations', ['org_id' => $this->test_org->org_id]);
        $this->assertDatabaseHas('roles', [
            'id' => $this->test_role->id,
            'name' => 'Test Role',
            'org_id' => $this->test_org->org_id,
        ]);
    }

    public function test_role_deletion_is_successful()
    {
        // Authenticate the admin user
        $this->actingAs($this->admin_user, 'api');

        // Perform the deletion
        $response = $this->deleteJson('/api/v1/organisations/' . $this->test_org->org_id . '/roles/' . $this->test_role->id);

        // Assert the response status and JSON structure
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status_code',
                'message',
            ]);

        // Verify the role has been deleted from the database
        $this->assertDatabaseMissing('roles', ['id' => $this->test_role->id]);
    }

    public function test_cannot_delete_non_existent_role()
    {
        // Authenticate the admin user
        $this->actingAs($this->admin_user, 'api');

        // Attempt to delete a non-existent role
        $response = $this->deleteJson('/api/v1/organisations/' . $this->test_org->org_id . '/roles/9999');

        // Assert a 404 Not Found response
        $response->assertStatus(404);
    }
}
