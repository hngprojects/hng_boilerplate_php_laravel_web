<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    //


    public function emailNotice($request)
    {

        return response()->json([
            'status_code' => 403,
            'message' => 'Email not verified. Please verify your email to continue.',
            'status' => 'error',
            'data' => []
        ], 403);
    }

    public function verifyEmail(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return response()->json([
            'status_code' => 200,
            'message' => 'Email verified successfully',
            'status' => 'success',
            'data' => []
        ], 200);
    }

    public function resendEmail(Request $request) 
    {
        $user = $request->user();

    if ($user->hasVerifiedEmail()) {
        return response()->json([
            'status_code' => 400,
            'message' => 'Email already verified.',
            'status' => 'error',
            'data' => []
        ], 400);
    }

    $user->sendEmailVerificationNotification();

    return response()->json([
        'status_code' => 200,
        'message' => 'Verification email resent successfully.',
        'status' => 'success',
        'data' => []
    ], 200);
    }
}
