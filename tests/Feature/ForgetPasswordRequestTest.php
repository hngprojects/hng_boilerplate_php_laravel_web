<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\ResetPasswordToken;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ForgetPasswordRequestTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */

    /** @test */
    public function it_fails_when_email_is_not_provided()
    {
        $response = $this->postJson('/api/v1/auth/password-reset-email', []);

        $response->assertStatus(422)
                ->assertJson([
                    'message' => [
                        'email' => [
                            'The email field is required.'
                        ]
                    ],
                    'status_code' => 422
                ]);
    }

    /** @test */
    public function it_returns_error_when_user_does_not_exist()
    {
        // Create a valid email but without an associated user
        $response = $this->postJson('/api/v1/auth/password-reset-email', [
            'email' => 'nonexistentuser@example.com',
        ]);

        $response->assertStatus(400)
                ->assertJson([
                    'message' => 'User does not exist',
                    'status_code' => 400
                ]);
    }

    /** @test */
    public function it_returns_error_for_invalid_email_domain()
    {
        // Provide an email with a domain that should not be in the database
        $response = $this->postJson('/api/v1/auth/password-reset-email', [
            'email' => 'test@invalid-domain.com',
        ]);

        $response->assertStatus(400)
                ->assertJson([
                    'message' => 'User does not exist',
                    'status_code' => 400
                ]);
    }

    /** @test */
    public function it_returns_error_for_email_with_invalid_format()
    {
        // Provide an invalid email format
        $response = $this->postJson('/api/v1/auth/password-reset-email', [
            'email' => 'invalid-email-format',
        ]);

        $response->assertStatus(422)  // Expect validation error for invalid email format
                ->assertJson([
                    'message' => [
                        'email' => [
                            'The email field must be a valid email address.'
                        ]
                    ],
                    'status_code' => 422
                ]);
    }

    /** @test */
    public function it_returns_error_when_email_field_is_empty()
    {
        // Provide an empty email field
        $response = $this->postJson('/api/v1/auth/password-reset-email', [
            'email' => '',
        ]);

        $response->assertStatus(422)  // Expect validation error for empty email
                ->assertJson([
                    'message' => [
                        'email' => [
                            'The email field is required.'
                        ]
                    ],
                    'status_code' => 422
                ]);
    }

    /** @test */
    public function it_fails_when_email_is_not_provided_via_token()
    {
        $response = $this->postJson('/api/v1/auth/forgot-password', []);
        $response->assertStatus(422)
                ->assertJson([
                    'message' => [
                        'email' => [
                            'The email field is required.'
                        ]
                    ],
                    'status_code' => 422
                ]);
    }

     /** @test */
     public function it_returns_error_when_user_does_not_exist_via_token()
     {
         // Create a valid email but without an associated user
         $response = $this->postJson('/api/v1/auth/forgot-password', [
             'email' => 'nonexistentuser@example.com',
         ]);
 
         $response->assertStatus(400)
                 ->assertJson([
                     'message' => 'User does not exist',
                     'status_code' => 400
                 ]);
     }

     /** @test */
    public function it_returns_error_for_invalid_email_domain_via_token()
    {
        // Provide an email with a domain that should not be in the database
        $response = $this->postJson('/api/v1/auth/forgot-password', [
            'email' => 'test@invalid-domain.com',
        ]);

        $response->assertStatus(400)
                ->assertJson([
                    'message' => 'User does not exist',
                    'status_code' => 400
                ]);
    }

    /** @test */
    public function it_returns_error_for_email_with_invalid_format_via_otp()
    {
        // Provide an invalid email format
        $response = $this->postJson('/api/v1/auth/forgot-password', [
            'email' => 'invalid-email-format',
        ]);

        $response->assertStatus(422)  // Expect validation error for invalid email format
                ->assertJson([
                    'message' => [
                        'email' => [
                            'The email field must be a valid email address.'
                        ]
                    ],
                    'status_code' => 422
                ]);
    }

    /** @test */
    public function it_returns_error_when_email_field_is_empty_via_otp()
    {
        // Provide an empty email field
        $response = $this->postJson('/api/v1/auth/forgot-password', [
            'email' => '',
        ]);

        $response->assertStatus(422)  // Expect validation error for empty email
                ->assertJson([
                    'message' => [
                        'email' => [
                            'The email field is required.'
                        ]
                    ],
                    'status_code' => 422
                ]);
    }

    /** @test */
    public function can_send_password_reset_email_via_otp()
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $randomNumber = Str::random(6);
        $token = substr($randomNumber, 0, 6);

        // Store the token in the password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => $token,
                'created_at' => Carbon::now(),
            ]
        );

        $response = $this->postJson('/api/v1/auth/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Password reset link sent',
                 ]);
    }
}
