<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use HttpResponses;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function register()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $request->validated($request->all());

        try {
            DB::beginTransaction();


            // creating the user 
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $token = JWTAuth::fromUser($user);
            DB::commit();

            return $this->success(data: [
                'accessToken' => $token,
                'user' => $user,
            ], status: true, message: "Registration successful", code: Response::HTTP_CREATED,);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration error: ' . $e->getMessage());
            return $this->error(
                status: false,
                message: "Registration unsuccessful",
                code: Response::HTTP_BAD_REQUEST,
            );
        }
    }

    /**
     * Display the specified resource.
     */

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials',
                'error' => 'authentication_failed',
                'statusCode' => 401
            ], 401);
        }

        $user = Auth::user();
        // $user->last_login_at = now();
        /** @var \App\Models\User $user **/
        $user->save();

        $name_list = explode(" ", $user->name);
        $first_name = current($name_list);
        if (count($name_list) > 1) {
            $last_name = end($name_list);
        } else {
            $last_name = "";
        }

        return response()->json([
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'signup_type' => $user->signup_type,
                    'is_active' => $user->is_active,
                    'is_verified' => $user->is_verified,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                    // 'last_login_at' => $user->last_login_at,
                ],
                'access_token' => $token,
                'refresh_token' => null // JWT does not inherently support refresh tokens; you might need to implement this yourself
            ]
        ], 200);
    }

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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
