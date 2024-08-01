<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use App\Mail\ContactInquiryMail;

class ContactControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testSendInquiryValidationError()
    {
        $response = $this->postJson('/api/v1/inquiries', [
            'name' => '',
            'email' => 'not-an-email',
            'message' => '',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Validation failed',
                'status_code' => 400,
                'errors' => [
                    'The name field is required.',
                    'The email field must be a valid email address.',
                    'The message field is required.'
                ],
            ]);
    }

    public function testSendInquiryServerError()
    {
        // Mock the mail sending to throw an exception
        Mail::fake();
        Mail::shouldReceive('to->queue')->andThrow(new \Exception('Simulated server error'));

        $response = $this->postJson('/api/v1/inquiries', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'This is a test message.',
        ]);

        $response->assertStatus(500)
            ->assertJson([
                'message' => 'A server error occurred',
                'status_code' => 500,
            ]);
    }

    public function testSendInquirySuccess()
    {
        // Mock the mail sending
        Mail::fake();

        $response = $this->postJson('/api/v1/inquiries', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'This is a test message.',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Inquiry successfully sent',
                'status_code' => 201,
            ]);

        // Verify that an email was queued
        Mail::assertQueued(ContactInquiryMail::class, function ($mail) {
            return $mail->hasTo('john@example.com');
        });
    }
}
