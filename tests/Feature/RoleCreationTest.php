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
        $this->test_user = User::create([
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password',
        ]);

        $this->test_org = Organisation::create([
            "name" => 'Test organisation',
            "user_id" => $this->test_user->id,
            "email" => "test email",
            "description" => "test description",
            "industry" => "test industry",
            "type" => "test type",
            "country" => "test country",
            "address" => "test address",
            "state" => "test state",
        ]);

        $this->test_permission = Permission::create([
            'name' => 'test permission 1'
        ]);

        $this->assertDatabaseHas('users', ['name'=>'Test User']);
        $this->assertDatabaseHas('organisations', ['name'=>'Test organisation']);
        $this->assertDatabaseHas('permissions', ['name'=>'test permission 1']);
    }

    public function test_role_creation_is_successful()
    {

        $this->postJson('/api/v1/roles', [
            'role_name' => 'Test role',
            'organisation_id' => $this->test_org->org_id,
            'permissions_id' => $this->test_permission->id,
        ])
            ->assertStatus(201)
            ->assertJsonStructure([
                'status_code',
                'message'
            ]);

        $this->assertDatabaseHas('roles', [
            'name' => 'Test role',
        ])->assertDatabaseCount('roles_permissions', 1);
    }

    public function test_role_creation_fails_on_validation_error()
    {

        $this->postJson('/api/v1/roles', [
            'role_name' => '',
            'organisation_id' => "",
            'permissions_id' => "",
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('role_name')
            ->assertJsonValidationErrorFor('organisation_id')
            ->assertJsonValidationErrorFor('permissions_id');

        $this->assertDatabaseEmpty('roles');
    }
}
