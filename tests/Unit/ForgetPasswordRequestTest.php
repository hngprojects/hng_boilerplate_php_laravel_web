<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ForgetPasswordRequestTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function it_sends_password_reset_notification()
    {
        Notification::fake();

        $user = User::factory()->create();
        // $url = 'http://example.com/reset-password?token=abc123&email=test@example.com';
        $token_key = Str::random(60);
        $token = Hash::make($token_key);
        $url = URL::temporarySignedRoute(
            'password.reset',
            now()->addMinutes(config('auth.passwords.users.expire')),
            ['token' => $token, 'email' => $user->email]
        );

        $user->sendPasswordResetNotification($url);

        Notification::assertSentTo(
            [$user], ResetPasswordNotification::class,
            function ($notification) use ($url) {
                return $notification->url === $url;
            }
        );

         // Simulate a request to the reset URL
         $response = $this->get($url);

         // Extract query parameters
         $query = parse_url($url, PHP_URL_QUERY);
         parse_str($query, $params);
 
         $this->assertEquals($token, $params['token']);
         $this->assertEquals($user->email, $params['email']);
    }

    /** @test */
    public function reset_password_notification_contains_correct_url()
    {
        $url = 'http://example.com/reset-password?token=abc123&email=test@example.com';
        $notification = new ResetPasswordNotification($url);

        $mailMessage = $notification->toMail((object) ['email' => 'test@example.com']);

        $this->assertStringContainsString($url, $mailMessage->actionUrl);
        $this->assertStringContainsString('Reset Password', $mailMessage->actionText);
        $this->assertStringContainsString('Reset Password Notification', $mailMessage->subject);
    }
}
