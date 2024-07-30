<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator; 

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }


    /**
     * updatePassword
     */
    public function updatePassword(Request $request)
{
    // Validation
    $request->validate([
        'old_password' => 'required',
        'new_password' => [
            'required',
            'confirmed',
            'min:8', // at least 8 characters
            'regex:/[A-Z]/', // at least one uppercase letter
            'regex:/[0-9]/' // at least one number
        ],
    ]);

    if (!Hash::check($request->old_password, Auth::user()->password)) {
        return response()->json([
            'Status' => 400,
            'message' => 'Old Password does not match!',
        ], 400);
    }

    // Update The New Password
    User::whereId(Auth::user()->id)->update([
        'password' => Hash::make($request->new_password)
    ]);

    return response()->json([
        'Status' => 200,
        'Message' => 'Password changed successfully',
    ], 200);
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
