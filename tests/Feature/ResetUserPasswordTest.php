<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ResetUserPasswordTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_resets_password_with_valid_token()
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        // Store the token in the password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => $token,
                'created_at' => Carbon::now(),
            ]
        );

        $response = $this->postJson("/api/v1/auth/request-password-request/{$token}", [
            'email' => $user->email,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword'
        ])->assertStatus(200)
        ->assertJson(['message' => 'Password reset successfully']);

        $this->assertTrue(Hash::check('newpassword', $user->fresh()->password));
        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => $user->email,
        ]);
    }

    /** @test */
    public function it_returns_error_with_invalid_token()
    {
        $user = User::factory()->create();
        $token = 'invalidtoken';

        $res = $this->postJson("/api/v1/auth/request-password-request/{$token}", [
            'email' => $user->email,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword'
        ])
            ->assertStatus(400)
            ->assertJson(['message' => 'Invalid token']);
    }

    /** @test */
    public function it_returns_error_with_expired_token()
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        // Simulate token expiration by setting created_at to a past time
        DB::table('password_reset_tokens')->where('email', $user->email)->update([
            'token' => $token,
            'created_at' => Carbon::now()->subHours(2)
        ]);

        $this->postJson("/api/v1/auth/request-password-request/{$token}", [
            'email' => $user->email,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword'
        ])->assertStatus(400)
            ->assertJson(['message' => 'Token has expired']);
    }

    /** @test */
    public function it_validates_reset_password_fields()
    {
        $token = '6ttr5eefcdccsdfgjhhtgtr54eewxvbnjhgb';

        $this->postJson("/api/v1/auth/request-password-request/{$token}", [
            'email' => 'notanemail',
            'password' => 'short',
            'password_confirmation' => 'notmatching'
        ])->assertStatus(400)
            ->assertJsonStructure(['message' => ['email', 'password']]);
    }
}
