<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\MagicLinkEmail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use RefreshDatabase;

class MagicLinkController extends Controller
{

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate'); // Run migrations for the test database
    }

    
    // Send a magic link to the user's email.
    public function sendMagicLink(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => 400,
                'status' => 'error',
                'message' => 'Invalid email address',
                'data' => $validator->errors(),
            ], 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status_code' => 404,
                'status' => 'error',
                'message' => 'User not found',
                'data' => [],
            ], 404);
        }

        // Generate a unique token
        $token = Str::random(60);
        $expiresAt = Carbon::now()->addMinutes(30); // expires in 30 minutes

        // Save the token to the user in db
        $user->magic_link_token = $token;
        $user->magic_link_expires_at = $expiresAt;
        $user->save();

        // Send email
        Mail::to($user->email)->send(new MagicLinkEmail($token));

        return response()->json([
            'status_code' => 200,
            'status' => 'success',
            'message' => 'Verification token sent to email',
        ], 200);
    }
}