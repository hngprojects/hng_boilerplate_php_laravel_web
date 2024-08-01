<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmailRequestsJob;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    public function triggerEmailSending(Request $request)
    {
        // Dispatch the job
        SendEmailRequestsJob::dispatch();
        
        return response()->json(['message' => 'Email requests are being processed.']);
    }
}
