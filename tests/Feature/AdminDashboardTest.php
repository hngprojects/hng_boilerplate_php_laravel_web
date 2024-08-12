<?php

namespace Tests\Feature;

use App\Models\User;
use App\Enums\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AdminDashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $regularUser;
    protected $adminToken;
    protected $userToken;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed the database with an admin user
        $this->admin = User::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'username' => 'admin_user',
            'email' => 'admin@example.com',
            'role' => Role::ADMIN->value,
            'avatar_url' => 'https://example.com/avatar.jpg',
            'invite_link' => 'https://example.com/invite/admin_user',
            'status' => true,
            'is_disabled' => false,
            'gender' => 'male',
            'dob' => '1980-01-01',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Seed the database with a regular user
        $this->regularUser = User::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'username' => 'regular_user',
            'email' => 'user@example.com',
            'role' => Role::USER->value,
            'avatar_url' => 'https://example.com/avatar.jpg',
            'invite_link' => 'https://example.com/invite/regular_user',
            'status' => true,
            'is_disabled' => false,
            'gender' => 'female',
            'dob' => '1995-01-01',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Generate tokens for admin and regular user
        $this->adminToken = JWTAuth::fromUser($this->admin);
        $this->userToken = JWTAuth::fromUser($this->regularUser);
    }

    public function test_admin_can_get_all_users()
    {
        $response = $this->withHeaders(['Authorization' => "Bearer $this->adminToken"])
                         ->getJson('/api/v1/users');
        $response->assertStatus(200)
         ->assertJsonStructure([
             'data' => [
                 '*' => [
                     'id',
                     'username',
                     'email',
                     'status',
                     'created_at',
                     'is_disabled',
                 ]
             ]
         ]);

    }

    public function test_non_admin_cannot_get_all_users()
    {
        $response = $this->withHeaders(['Authorization' => "Bearer $this->userToken"])
                         ->getJson('/api/v1/users');

        $response->assertStatus(401); 
    }
        
}
