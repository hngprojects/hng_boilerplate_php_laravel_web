<?php

namespace Tests\Feature;

use App\Jobs\SendEmailRequestsJob;
use App\Mail\EmailRequestMailable;
use App\Models\EmailRequest;
use App\Models\EmailTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class SendEmailRequestsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
    }

    public function test_it_creates_email_request()
    {
        // Create a template
        $template = EmailTemplate::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'title' => 'Welcome Template',
            'template' => '<p>Hello {{name}},</p><p>Welcome to our service!</p>',
            'status' => true,
        ]);

        // Define request data
        $requestData = [
            'template_id' => $template->id,
            'subject' => 'Test Subject',
            'recipient' => 'test@example.com',
            'variables' => '{"name": "John Doe"}',
            'status' => 'pending',
        ];

        // Send POST request to create email request
        $response = $this->postJson('/api/v1/email-requests', $requestData);

        // Assert the response
        $response->assertStatus(200)
                 ->assertJson(['message' => 'Email request is queued.']);

        // Assert that the email request was created
        $this->assertDatabaseHas('email_requests', [
            'template_id' => $template->id,
            'subject' => 'Test Subject',
            'recipient' => 'test@example.com',
            'variables' => '{"name": "John Doe"}',
            'status' => 'pending',
        ]);
    }

    public function test_it_returns_validation_errors_for_invalid_data()
    {
        // Define invalid request data
        $invalidData = [
            'template_id' => 'invalid',
            'subject' => '', 
            'recipient' => 'invalid-email',
            'variables' => 'not-json',
            'status' => 'invalid-status', 
        ];

        // Send POST request with invalid data
        $response = $this->postJson('/api/v1/email-requests', $invalidData);

        // Assert the response status and JSON structure
        $response->assertStatus(400)
                 ->assertJson(function (AssertableJson $json) {
                     $json->has('error')
                          ->whereType('error', 'array');
                 });
    }

    public function test_job_sends_emails()
    {
    // Create a template
    $template = EmailTemplate::create([
        'id' => (string) \Illuminate\Support\Str::uuid(),
        'title' => 'Welcome Template',
        'template' => '<p>Hello {{name}},</p><p>Welcome to our service!</p>',
        'status' => true,
    ]);

    // Create an email request
    $request = EmailRequest::create([
        'id' => (string) \Illuminate\Support\Str::uuid(),
        'template_id' => $template->id,
        'subject' => 'Test Subject',
        'recipient' => 'test@example.com',
        'variables' => '{"name": "John Doe"}',
        'status' => 'pending',
    ]);

    SendEmailRequestsJob::dispatch();

    // Assert the email was sent
    Mail::assertSent(EmailRequestMailable::class, function (EmailRequestMailable $mail) use ($request) {
        return $mail->hasTo($request->recipient) &&
               $mail->subject === $request->subject &&
               $mail->htmlContent === '<p>Hello John Doe,</p><p>Welcome to our service!</p>'; 
    });

    $request->refresh();
    $this->assertEquals('sent', $request->status);
    }

    public function test_job_logs_error_when_email_sending_fails()
    {
        // Create a template
        $template = EmailTemplate::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'title' => 'Welcome Template',
            'template' => '<p>Hello {{name}},</p><p>Welcome to our service!</p>',
            'status' => true,
        ]);

        // Create an email request
        $request = EmailRequest::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'template_id' => $template->id,
            'subject' => 'Test Subject',
            'recipient' => 'test@example.com',
            'variables' => '{"name": "John Doe"}',
            'status' => 'pending',
        ]);

        Log::spy();

        // Simulate an exception when sending email
        Mail::shouldReceive('send')->andThrow(new \Exception('Mail sending failed'));

        // Dispatch the job
        SendEmailRequestsJob::dispatch();

        // Assert that an error was logged
        Log::assertLogged('error', function ($message, $context) use ($request) {
            return str_contains($message, 'Failed to send email request ' . $request->id) &&
                   str_contains($context['exception'], 'Mail sending failed');
        });

        $request->refresh();
        $this->assertEquals('pending', $request->status);
    }

}
