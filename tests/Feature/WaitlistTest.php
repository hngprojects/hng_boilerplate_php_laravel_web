<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use App\Mail\WaitlistConfirmation;


class WaitlistTest extends TestCase
{

    // Waitlist posting test
    public function test_waitlist_posting(): void
    {
        $response = $this->post('api/v1/waitlist', [
            'email'=> fake()->safeEmail(),
            'full_name'=> fake()->name(),
        ]);

        $response->assertStatus(201);
    }

    // Waitlist mailing test
    public function test_waitlist_confirmation_mailing(): void
    {
        // Fake the mail sending
        Mail::fake();

        // Prepare the data
        $waitlist_user = [
            'name' => 'Paulson Bosah',
            'email' => 'paulsonbosah@example.com'
        ];

        // Send the email
        Mail::to($waitlist_user['email'])->send(new WaitlistConfirmation($waitlist_user));

        // Assert that the email was sent
        Mail::assertSent(WaitlistConfirmation::class, function ($mail) use ($waitlist_user) {
            return $mail->hasTo($waitlist_user['email']) &&
                   $mail->waitlist_user['name'] === $waitlist_user['name'];
        });
    }

}
