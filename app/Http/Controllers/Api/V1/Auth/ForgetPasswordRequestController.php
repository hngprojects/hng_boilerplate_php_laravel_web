<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\HttpResponses;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ForgetPasswordRequestController extends Controller
{
    use HttpResponses;

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email:rfc'
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(message:  $validator->errors(), status_code: 422);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->apiResponse(message:  'User does not exist', status_code: 400);
        }

        // Create a new token
        $token_key = Str::random(30);
        $token = Hash::make($token_key);

        // Store the token in the password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $token,
                'created_at' => Carbon::now(),
            ]
        );

        // Send the reset password email
        $url = URL::temporarySignedRoute(
            'password.reset',
            Carbon::now()->addMinutes(config('auth.passwords.users.expire')),
            ['token' => $token, 'email' => $request->email]
        );
        
        // $url = env('APP_URL') . "/api/v1/auth/password-reset-email?email=" . urlencode($request->email) . "&token=" . urlencode($token) . "&expires=" . Carbon::now()->addMinutes(config('auth.passwords.users.expire'))->timestamp;

        $user->sendPasswordResetNotification($url);

        return $this->apiResponse(message:  'Password reset link sent');

    }
}
