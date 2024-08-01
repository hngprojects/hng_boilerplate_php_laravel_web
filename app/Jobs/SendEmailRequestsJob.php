<?php

namespace App\Jobs;

use App\Mail\EmailRequestMailable;
use App\Models\EmailRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailRequestsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

   /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('SendEmailRequestsJob is being handled');
        // Fetch pending email requests
        $requests = EmailRequest::where('status', 'pending')->get();

        foreach ($requests as $request) {
            Log::info('Processing request: ' . $request->id);
            // Send the email
            Mail::to($request->recipient)->send(new EmailRequestMailable($request));
            
            // Update status to 'queued'
            $request->status = 'queued';
            $request->save();
        }
    }
}
