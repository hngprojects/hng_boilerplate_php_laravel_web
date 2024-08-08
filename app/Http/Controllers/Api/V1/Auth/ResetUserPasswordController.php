<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\HttpResponses;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\Rules\Password;

class ResetUserPasswordController extends Controller
{
    use HttpResponses;
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, $token)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email:rfc',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(message: $validator->errors(), status_code: 400);
        }

        // Check if the token exists in the password_reset_tokens table
        $passwordReset = DB::table('password_reset_tokens')->where([
            ['email', $request->email],
            ['token', $token],
        ])->first();

        // If the token is invalid, return an error
        if (!$passwordReset) {
            return $this->apiResponse(message: 'Invalid token', status_code: 400);
        }

        // Check if the token has expired (tokens are typically valid for one hour)
        if (Carbon::parse($passwordReset->created_at)->addMinutes(config('auth.passwords.users.expire'))->isPast()) {
            return $this->apiResponse(message: 'Token has expired', status_code: 400);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->apiResponse(message: 'User does not exist', status_code: 400);
        }

        // Reset the password
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the password reset token after successful reset
        // DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        DB::table('password_reset_tokens')->where([
            ['email', $request->email],
            ['token', $token],
        ])->delete();
        
        return $this->apiResponse(message: 'Password reset successfully', status_code: 200);
    }
}
