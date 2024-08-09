<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function stats()
    {
        $totalUsers = User::count();
        $totalDeletedUsers = User::onlyTrashed()->count();
        $totalActiveUsers = User::where('is_active', 1)->count() - $totalDeletedUsers;
        $totalInActiveUsers = User::where('is_active', 0)->count();

        return response()->json(
            [
                "status_code" => 200,
                "message" => "User statistics retrieved successfully",
                "total_users" => $totalUsers,
                "deleted_users" => $totalDeletedUsers,
                "active_users" => $totalActiveUsers,
                "in_active_users" => $totalInActiveUsers,
            ],
            200
        );
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::latest()->paginate();


        return response()->json(
            [
                "status_code" => 200,
                "message" => "Users returned successfully",
                "data" =>$users
            ],
            200
        );
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
        $validator = Validator::make($request->all(), [
            "name" => 'nullable|string',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            "email" => 'nullable|string|email|max:255|unique:users,email,' . $id,
            "phone" => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => "error",
                "message" => "User failed to update",
                "errors" => $validator->errors(),
                "status_code" => 400
            ], 400);
        }

        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'status' => "error",
                "message" => "User not found",
                "status_code" => 404
            ], 404);
        }

        $data = $request->only('name', 'email', 'phone');

        $data = array_map(function ($value) {
            return $value === '' ? null : $value;
        }, $data);

        $user->update($data);

        if ($user->profile) {
            $user->profile()->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name
            ]);
        } else {
            $user->profile()->create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name
            ]);
        }


        return response()->json(
            [
                "status" => "success",
                "message" => "User Updated Successfully",
                "user" => $user
            ],
            200
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status_code' => 404,
                'message' => 'User not found'
            ], 404);
        }

        $user->delete();
        return response()->noContent();
    }

}
