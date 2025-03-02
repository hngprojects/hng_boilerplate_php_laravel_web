<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserDeletionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user not found during deletion.
     *
     * @return void
     */
    public function test_user_not_found_during_deletion()
    {
        // Create an admin user
        $admin = User::create([
            'id' => '9cd7a1b4-92e9-434e-8f1d-be37d64d2835',
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('@Bulldozer01'), // Admin password
            'role' => 'admin',
            'is_verified' => 1,
            'email_verified_at' => now()
        ]);

        // Attempt to delete a non-existent user
        $response = $this->actingAs($admin)->deleteJson('/api/v1/users/f1c7a1b4-92e9-434e-8f1d-be37d64d2859');
        $response->assertStatus(404)
            ->assertJson([
                'statusCode' => 404,
                'message' => 'User not found',
            ]);
    }

    /**
     * Test unauthorized user attempting to delete another user.
     *
     * @return void
     */
    public function test_unauthorized_user_deleting_another_user()
    {
        // Create dummy users
        $user = User::create([
            'id' => 'f1c7a1b4-92e9-434e-8f1d-be37d64d2837',
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'is_verified' => 1,
            'email_verified_at' => now()
        ]);

        $anotherUser = User::create([
            'id' => 'f2c7a1b4-92e9-434e-8f1d-be37d64d2838',
            'name' => 'Another User',
            'email' => 'anotheruser@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'is_verified' => 1,
        ]);

        // Attempt to delete another user as a regular user
        $response = $this->actingAs($user)->deleteJson("/api/v1/users/{$anotherUser->id}");
        $response->assertStatus(403)
            ->assertJson([
                'statusCode' => 403,
                'message' => 'Unauthorized to delete this user',
            ]);
    }

    /**
     * Test admin deleting a user.
     *
     * @return void
     */
    public function test_admin_deleting_user()
    {
        // Create dummy users
        $admin = User::create([
            'id' => '9cd7a1b4-92e9-434e-8f1d-be37d64d2835',
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('@Bulldozer01'), // Admin password
            'role' => 'admin',
            'is_verified' => 1,
            'email_verified_at' => now()
        ]);

        $user = User::create([
            'id' => 'f1c7a1b4-92e9-434e-8f1d-be37d64d2837',
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'is_verified' => 1,
        ]);

        // Delete a user as an admin
        $response = $this->actingAs($admin)->deleteJson("/api/v1/users/{$user->id}");
        $response->assertStatus(200)
            ->assertJson([
                'statusCode' => 200,
                'message' => 'User deleted successfully',
            ]);
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    /**
     * Test super admin deleting a user.
     *
     * @return void
     */
    public function test_super_admin_deleting_user()
    {
        // Create dummy users
        $superAdmin = User::create([
            'id' => 'e0dba1b4-92e9-434e-8f1d-be37d64d2836',
            'name' => 'Super Admin User',
            'email' => 'superadmin@example.com',
            'password' => bcrypt('@Bulldozer01'),
            'role' => 'superAdmin',
            'is_verified' => 1,
            'email_verified_at' => now()
        ]);

        $anotherUser = User::create([
            'id' => 'f3c7a1b4-92e9-434e-8f1d-be37d64d2839',
            'name' => 'Another User 2',
            'email' => 'anotheruser2@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'is_verified' => 1,
        ]);

        // Delete a user as a super admin
        $response = $this->actingAs($superAdmin)->deleteJson("/api/v1/users/{$anotherUser->id}");
        $response->assertStatus(200)
            ->assertJson([
                'statusCode' => 200,
                'message' => 'User deleted successfully',
            ]);
        $this->assertSoftDeleted('users', ['id' => $anotherUser->id]);
    }

    /**
     * Test deleting self as super admin.
     *
     * @return void
     */
    public function test_deleting_self_as_super_admin()
    {
        // Create a super admin user
        $superAdmin = User::create([
            'id' => 'e0dba1b4-92e9-434e-8f1d-be37d64d2836',
            'name' => 'Super Admin User',
            'email' => 'superadmin@example.com',
            'password' => bcrypt('@Bulldozer01'),
            'role' => 'superAdmin',
            'is_verified' => 1,
            'email_verified_at' => now()
        ]);

        // Delete self as a super admin
        $response = $this->actingAs($superAdmin)->deleteJson("/api/v1/users/{$superAdmin->id}");
        $response->assertStatus(200)
            ->assertJson([
                'statusCode' => 200,
                'message' => 'User deleted successfully',
            ]);
        $this->assertSoftDeleted('users', ['id' => $superAdmin->id]);
    }
}
