<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;

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
                    'status' => 'Error',
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
                    'status' => 'Error',
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
                    'status' => 'Error',
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
                    'status' => 'Error',
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
                    'status' => 'Error',
                    'message' => [
                        'email' => [
                            'The email field is required.'
                        ]
                    ],
                    'status_code' => 422
                ]);
    }

    /** @test */
    public function can_send_password_reset_email()
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $token_key = Str::random(60);
        $token = Hash::make($token_key);

        $response = $this->postJson('/api/v1/auth/password-reset-email', [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'Success',
                     'message' => 'Password reset link sent',
                 ]);

        // Assert a notification was sent
        Notification::assertSentTo(
            [$user],
            ResetPasswordNotification::class,
            function ($notification) use ($token, $user) {
                // Extract the URL from the notification
                $url = $notification->toMail($user)->actionUrl;

                // Extract query parameters from the URL
                $query = parse_url($url, PHP_URL_QUERY);
                parse_str($query, $params);

                // Verify  email in the URL
                return $params['email'] === $user->email;
            }
        );

        // Check if token is saved in the database
        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'test@example.com',
        ]);
    }
}
