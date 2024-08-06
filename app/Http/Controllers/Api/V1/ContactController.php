<?php

namespace App\Http\Controllers\Api\V1;


use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\ContactInquiryMail;

class ContactController extends Controller
{

    public function index()
    {
        $inquiry = Inquiry::all();

        return response()->json([
            'status_code' => 200,
            'message' => "Inquiries returned successfully",
            'data' => $inquiry
        ], 200);

    }
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

            Inquiry::create($data);

            Mail::to($request->email)->queue(new ContactInquiryMail($data));

            return response()->json([
                'message' => 'Inquiry successfully sent',
                'status_code' => 201,

            ], 201);
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'A server error occurred',
                'status_code' => 500,
            ], 500);
        }
    }
}
