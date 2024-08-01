<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use App\Traits\HttpResponses;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;

class ForgetResetPasswordController extends Controller
{
    use HttpResponses;

    /**
     * Handle the incoming request.
     */
    public function forgetPassword(Request $request)
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
        $randomNumber = Str::random(6);
        $token = substr($randomNumber, 0, 6);

        // Store the token in the password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $token,
                'created_at' => Carbon::now(),
            ]
        );
        
        $user->sendPasswordResetToken($token);

        return $this->apiResponse(message:  'Password reset link sent');
    }

    /**
     * Handle the incoming request.
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email:rfc',
            'password' => ['required', 'string', Password::min(8)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->symbols()
            ->uncompromised()],
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(message: $validator->errors(), status_code: 400);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->apiResponse(message: 'User does not exist', status_code: 400);
        }

        // Reset the password
        $user->password = Hash::make($request->password);
        $user->save();
        
        return $this->apiResponse(message: 'Password reset successfully', status_code: 200);
    }

    /**
     * Handle the incoming request.
     */
    public function verifyUserOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email:rfc',
            'otp' => 'required|string|min:6|max:6',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(message: $validator->errors(), status_code: 400);
        }

        // Check if the token exists in the password_reset_tokens table
        $passwordReset = DB::table('password_reset_tokens')->where([
            ['email', $request->email],
            ['token', $request->otp],
        ])->first();

        // If the token is invalid, return an error
        if (!$passwordReset) {
            return $this->apiResponse(message: 'Invalid token', status_code: 400);
        }

        // Delete the password reset token after successful reset
        DB::table('password_reset_tokens')->where([
            ['email', $request->email],
            ['token', $request->otp],
        ])->delete();
        
        return $this->apiResponse(message: 'Token Validated Successfully', status_code: 200);
    }
}
