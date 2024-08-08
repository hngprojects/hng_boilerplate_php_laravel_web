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
        $requests = EmailRequest::with('template')->where('status', 'pending')->get();

        foreach ($requests as $request) {
            $subject = $request->subject;
            $template = $request->template;
            $htmlContent = $template->template;

            if (isset($request->variables)) {
                if (is_string($request->variables)) {
                    $variables = json_decode($request->variables, true);
                } else {
                    $variables = $request->variables;
                }

                if (is_array($variables)) {
                    // Replace placeholders in the email template with actual values
                    foreach ($variables as $key => $value) {
                        $placeholder = '{{' . $key . '}}';
                        if (strpos($htmlContent, $placeholder) !== false) {
                            $htmlContent = str_replace($placeholder, $value, $htmlContent);
                        } else {
                            Log::warning('Placeholder not found in HTML content:', ['placeholder' => $placeholder]);
                        }
                    }
                } else {
                }
            } else {
            }

            try {
                // Send the email using the Mailable class
                Mail::to($request->recipient)
                    ->send(new EmailRequestMailable($variables, $htmlContent, $subject));

                // Update request status
                $request->update(['status' => 'sent']);
            } catch (\Exception $e) {
                Log::error('Failed to send email request ' . $request->id . ': ' . $e->getMessage());
            }
        }
    }
}
