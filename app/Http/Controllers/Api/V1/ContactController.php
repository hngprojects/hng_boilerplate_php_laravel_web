<?php

namespace App\Http\Controllers\Api\V1;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Mail\ContactInquiryMail;

class ContactController extends Controller
{
    public function sendInquiry(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()->all(),
                'status_code' => 400,
            ], 400);
        }

        try {
            $data = $request->only(['name', 'email', 'message']);
            

            Mail::to('amowogbajegideon@gmail.com')->queue(new ContactInquiryMail($data));
            return response()->json([
                'message' => 'Inquiry successfully sent',
                'status_code' => 200,
                
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error sending inquiry: '.$e->getMessage());
            return response()->json([
                'message' => 'A server error occurred',
                'status_code' => 500,
            ], 500);
        }
    }
}

