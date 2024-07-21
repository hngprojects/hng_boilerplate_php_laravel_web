<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return User::query()
            ->paginate();
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
    public function show(User $user)
    {
        return $user->load('profile', 'products', 'organisations');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Update the password of the authenticated user.
     */
    public function updatePassword(Request $request)
    {
        // Validation rules
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => [
                'required',
                'string',
                'min:8', // Minimum 8 characters
                Password::min(8)
                    ->mixedCase() // Require at least one uppercase and one lowercase letter
                    ->letters() // Require at least one letter
                    ->numbers() // Require at least one number
                    ->symbols() // Require at least one symbol
            ],
        ]);
    
        // Return validation errors
        if ($validator->fails()) {
            return response()->json([
                'status' => 'unsuccessful',
                'message' => $validator->errors()->first(),
            ], 400);
        }
    
        // Authenticate user
        $user = auth()->user();
    
        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status_code' => 400,
                'message' => 'Current password is incorrect',
            ], 400);
        }
    
        // Update to new password
        $user->password = Hash::make($request->new_password);
        $user->save();
    
        return response()->json([
            'status' => 'success',
            'message' => 'Password updated successfully',
        ], 201);
    }
}
