<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        $validator = Validator::make($request->all(), [
            "name" => 'nullable|string',
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
        //
    }
}
