<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Support\Facades\Log;


class UpdatePasswordTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user to test with
        $this->user = User::factory()->create([
            'password' => Hash::make('OldPassword123'),
        ]);
    }

    public function test_successful_password_change()
    {
        $response = $this->actingAs($this->user, 'api')->postJson('/api/v1/password-update', [
            'old_password' => 'OldPassword123',
            'new_password' => 'NewPassword123',
            'new_password_confirmation' => 'NewPassword123',
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'Status' => 200,
                    'Message' => 'Password changed successfully',
                ]);

        $this->assertTrue(Hash::check('NewPassword123', $this->user->fresh()->password));
    }

    public function test_old_password_does_not_match()
    {
        $response = $this->actingAs($this->user, 'api')->postJson('/api/v1/password-update', [
            'old_password' => 'WrongOldPassword',
            'new_password' => 'NewPassword123',
            'new_password_confirmation' => 'NewPassword123',
        ]);

        $response->assertStatus(400)
                ->assertJson([
                    'Status' => 400,
                    'message' => 'Old Password does not match!',
                ]);
    }



    public function test_new_password_does_not_meet_requirements()
    {
        $response = $this->actingAs($this->user, 'api')->postJson('/api/v1/password-update', [
            'old_password' => 'OldPassword123',
            'new_password' => 'short',
            'new_password_confirmation' => 'short',
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['new_password']);
    }


    public function test_new_password_confirmation_does_not_match()
    {
        $response = $this->actingAs($this->user, 'api')->postJson('/api/v1/password-update', [
            'old_password' => 'OldPassword123',
            'new_password' => 'NewPassword123',
            'new_password_confirmation' => 'DifferentPassword123',
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['new_password']);
    }

}