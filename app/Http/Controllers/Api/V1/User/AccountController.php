<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountDeactivatedMail;

class AccountController extends Controller
{
    public function deactivate(Request $request)
    {
        $this->validate($request, [
            'confirmation' => 'required|boolean',
            'reason' => 'nullable|string',
        ]);

        if (!$request->confirmation) {
            return response()->json([
                'status_code' => 400,
                'error' => 'Confirmation needs to be true for deactivation'
            ], 400);
        }

        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status_code' => 401,
                'error' => 'Could not validate user credentials'
            ], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'status_code' => 400,
                'error' => 'User has been deactivated'
            ], 400);
        }

        // Deactivate user
        $user->is_active = false;
        $user->save();

        \Log::info('User deactivated: ', ['user_id' => $user->id, 'is_active' => $user->is_active]);
        
        // Send mail
        Mail::to($user->email)->send(new AccountDeactivatedMail($user));

        return response()->json([
            'status_code' => 200,
            'message' => 'Account Deactivated Successfully'
        ], 200);
    }
}
