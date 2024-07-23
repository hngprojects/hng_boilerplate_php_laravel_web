<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountDeactivatedMail;
use App\Models\User;

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
                'error' => 'Confimation needs to be true for deactivation'
            ], 400);
        }

        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status_code' => 401,
                'error' => 'Could not validate user credentials'
            ], 401);
        }

        if ($user->status == 'deactivated') {
            return response()->json([
                'status_code' => 400,
                'error' => 'User has been deactivated'
            ], 400);
        }

        // Deactivate user
        $user->status = 'deactivated';
        $user->deactivation_reason = $request->reason;
        $user->save();

        // Send mail
        Mail::to($user->email)->send(new AccountDeactivatedMail($user));

        return response()->json([
            'status_code' => 200,
            'message' => 'Account Deactivated Successfully'
        ], 200);
    }
}
