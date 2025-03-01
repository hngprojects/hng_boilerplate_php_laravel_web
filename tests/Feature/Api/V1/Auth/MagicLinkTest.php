<?php

namespace Tests\Feature\Api\V1\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\MagicLinkEmail;
use Illuminate\Support\Facades\Log;
use Exception;

class MagicLinkTest extends TestCase
{

    use RefreshDatabase;
    
    // Test that a magic link email is sent for a valid email.
    public function test_magic_link_email_is_sent_for_valid_email()
    {

        Mail::fake();
        
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $response = $this->postJson('/api/v1/auth/magic-link', [
            'email' => $user->email,
        ]);


        $response->assertStatus(200)
                 ->assertJson([
                     'status_code' => 200,
                     'status' => 'success',
                     'message' => 'Verification token sent to email',
                 ]);

        Mail::assertSent(MagicLinkEmail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }
    // End of method


    // Test that an error is returned for an invalid email format.
    public function test_invalid_email_format_returns_error()
    {
        
        Mail::fake();

        // Make a request with an invalid email format
        $response = $this->postJson('/api/v1/auth/magic-link', [
            'email' => 'invalid-email',
        ]);

        $response->assertStatus(400)
                 ->assertJson([
                     'status_code' => 400,
                     'status' => 'error',
                     'message' => 'Invalid email address',
                 ]);

        Mail::assertNotSent(MagicLinkEmail::class);
    }
    // End of method


    // Test that an error is returned for a non-existent email
    public function test_non_existent_email_returns_error()
    {
        
        Mail::fake();

        // Make a request with a non-existent email
        $response = $this->postJson('/api/v1/auth/magic-link', [
            'email' => 'nonexistent@example.com',
        ]);

    
        $response->assertStatus(404)
                 ->assertJson([
                     'status_code' => 404,
                     'status' => 'error',
                     'message' => 'User not found',
                 ]);

        Mail::assertNotSent(MagicLinkEmail::class);
    }
    // End of method


    // Test that an error is returned if email sending fails
    public function test_email_sending_failure_returns_error()
    {
        Mail::shouldReceive('to->send')
                ->once()
                ->andThrow(new Exception('Failed to send email'));
    
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);
    
        $response = $this->postJson('/api/v1/auth/magic-link', [
            'email' => $user->email,
        ]);
    
        $response->assertStatus(500)
                 ->assertJson([
                     'status_code' => 500,
                     'status' => 'error',
                     'message' => 'Failed to send email'
                 ]);
    }
    
    // End of method
}