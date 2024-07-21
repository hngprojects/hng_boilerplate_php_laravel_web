<?php

namespace Tests\Unit;

use App\Models\Organisation;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleCreationTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     */
    public function test_role_creation()
    {
        $this->postJson('/api/v1/roles', [
            'role_name' => 'Test role',
            'organisation_id' => Organisation::create([
                "name" => 'Test organisation',
                "user_id" => User::create([
                    'name' => 'Test User',
                    'email' => 'testuser@example.com',
                    'password' => 'password',
                ])->id,
                "email" => "test email",
                "description" => "test description",
                "industry" => "test industry",
                "type" => "test type",
                "country" => "test country",
                "address" => "test address",
                "state" => "test state",
            ])->org_id,
            'permissions_id' => Permission::create([
                'name' => 'test permision 1'
            ])->id,
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
}
