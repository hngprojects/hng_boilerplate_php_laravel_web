<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Organisation;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Api\V1\RoleController;
use App\Http\Requests\StoreRoleRequest;

class DisableRoleTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $user;
    protected $organisation;
    protected $role;
    protected $defaultRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organisation = Organisation::factory()->create();

        $this->admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->user = User::factory()->create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->role = Role::create([
            'name' => 'Test Role',
            'org_id' => $this->organisation->org_id,
            'is_active' => true,
        ]);

        $this->defaultRole = Role::create([
            'name' => 'Default Role',
            'org_id' => $this->organisation->org_id,
            'is_default' => true,
        ]);

        $this->admin->roles()->attach($this->role->id);
        $this->user->roles()->attach($this->role->id);
    }

    public function testDisableRoleUnauthorized()
    {
        $controller = new RoleController();

        $request = StoreRoleRequest::create("/api/v1/organisations/{$this->organisation->org_id}/roles/{$this->role->id}/disable", 'PUT');
        $request->setUserResolver(function () {
            return $this->user;
        });

        $response = $controller->disableRole($request, $this->organisation->org_id, $this->role->id);

        // Check if the user without admin privileges gets a 403 response
        $this->assertEquals(403, $response->status());
    }
}
