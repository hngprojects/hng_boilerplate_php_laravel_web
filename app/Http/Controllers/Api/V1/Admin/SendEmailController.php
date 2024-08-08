<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmailRequestsJob;
use App\Mail\EmailRequestMailable;
use App\Models\EmailRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class SendEmailController extends Controller
{
    public function createEmailRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'template_id' => 'required|uuid|exists:email_templates,id',
            'subject' => 'required|string|max:255',
            'recipient' => 'required|email',
            'variables' => 'nullable|string',
            'status' => 'required|in:pending,sent,failed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 400);
        }

        // Create a new email request
        $emailRequest = EmailRequest::create([
            'template_id' => $request->input('template_id'),
            'subject' => $request->input('subject'),
            'recipient' => $request->input('recipient'),
            'variables' => $request->input('variables', '{}'),
            'status' => $request->input('status', 'pending'),
        ]);

        return response()->json(['message' => 'Email request is queued.']);
    }
    public function triggerEmailSending(Request $request)
    {
        SendEmailRequestsJob::dispatch();
        
        return response()->json(['message' => 'Email requests are being processed.']);
    }
}
