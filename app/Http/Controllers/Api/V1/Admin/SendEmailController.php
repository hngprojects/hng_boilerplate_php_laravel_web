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
        // $validator = Validator::make($request->all(), [
        //     'template_id' => 'integer|min:1',
        //     'variables' => 'integer|min:1|max:100',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'error' => 'Invalid pagination parameters'
        //     ], 400);
        // }

        EmailRequest::create([
            $request->all(),
        ]);

        return response()->json(['message' => 'Email requests are being processed.']);
    }
    public function triggerEmailSending(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'template_id' => 'integer|min:1',
        //     'variables' => 'integer|min:1|max:100',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'error' => 'Invalid pagination parameters'
        //     ], 400);
        // }

        //template id
        //variables

        // $emailrequests = EmailRequest::with('template')->where('status', 'pending')->get();

        // foreach ($emailrequests as $emailrequest) {
        //     Mail::to($emailrequest->receipient)->send(new EmailRequestMailable($emailrequest->template->content));
        // }
        // Dispatch the job
        SendEmailRequestsJob::dispatch();
        
        return response()->json(['message' => 'Email requests are being processed.']);
    }
}
