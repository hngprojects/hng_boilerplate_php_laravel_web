<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Organisation;
use App\Models\Role;
use App\Models\User;

class RoleUpdateTest extends TestCase
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

}
