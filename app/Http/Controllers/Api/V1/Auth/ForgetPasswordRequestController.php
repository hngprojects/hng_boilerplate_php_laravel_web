<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\HttpResponses;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use App\Models\User;
use Illuminate\Support\Facades\Password;

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
        $token = Password::createToken($user);

        // Store the token in the password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $token,
                'created_at' => Carbon::now(),
            ]
        );

        // Check if RESET_PASSWORD_LINK is defined in .env
        $url = env('RESET_PASSWORD_LINK');

        if ($url) {
            // Use the URL from the .env file and append the token and email
            $url = $url . '?token=' . $token . '&email=' . urlencode($request->email);
        } else {
            // Generate a temporary signed route
            $url = URL::temporarySignedRoute(
                'password.reset',
                Carbon::now()->addMinutes(config('auth.passwords.users.expire')),
                ['token' => $token, 'email' => $request->email]
            );
        }
        
        $user->sendPasswordResetNotification($url);

        return $this->apiResponse(message:  'Password reset link sent');

    }
}
