<?php

namespace Tests\Feature;

use App\Jobs\SendEmailRequestsJob;
use App\Mail\EmailRequestMailable;
use App\Models\EmailRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendEmailRequestsTest extends TestCase
{
    public function test_job_sends_emails()
    {
        Mail::fake();
    $requests = EmailRequest::factory()->count(3)->create(['status' => 'pending']);

    SendEmailRequestsJob::dispatch();

    foreach ($requests as $request) {
        Mail::assertQueued(EmailRequestMailable::class, function ($mail) use ($request) {
            return $mail->emailRequest->id === $request->id;
        });

        $this->assertEquals('queued', $request->fresh()->status);
    }
    }
}
