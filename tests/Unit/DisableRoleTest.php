<?php

namespace Tests\Unit;

use Tests\TestCase;
// use PHPUnit\Framework\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Organisation;
use App\Models\Role;

class DisableRoleTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     */
    public function testDisableRoleSuccess() {
        $admin = User::factory()->create(['role' => 'admin']);
        $organization = Organisation::factory()->create();
        $role = Role::factory()->create(['org_id' => $organization->id, 'is_active' => true]);
        $defaultRole = Role::factory()->create(['org_id' => $organization->id, 'is_default' => true]);

        $response = $this->actingAs($admin, 'api')->put("/api/v1/organizations/{$organization->id}/roles/{$role->id}/disable");

        $response->assertStatus(200);
        $this->assertFalse($role->fresh()->is_active);
        $this->assertCount(0, $role->users);
        $this->assertCount(1, $defaultRole->users);
    }

    public function testDisableRoleUnauthorized() {
        $user = User::factory()->create();
        $organization = Organisation::factory()->create();
        $role = Role::factory()->create(['org_id' => $organization->id, 'is_active' => true]);

        $response = $this->actingAs($user, 'api')->put("/api/v1/organizations/{$organization->id}/roles/{$role->id}/disable");

        $response->assertStatus(403);
    }

}
