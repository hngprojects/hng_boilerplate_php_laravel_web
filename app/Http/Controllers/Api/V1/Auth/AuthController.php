<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use App\Models\Organisation;
use App\Models\OrganisationUser;
use Illuminate\Support\Facades\Log;
use App\Models\Validators\AuthValidator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

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
    public function store(Request $request)
    {
         // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email:rfc|max:255|unique:users',
            'admin_secret' => 'nullable|string|max:255',
            'password' => ['required', 'string', Password::min(8)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->symbols()
            ->uncompromised()],
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return $this->apiResponse(message: $validator->errors(), status_code: 400);
        }

        try {
            DB::beginTransaction();

            $role = $request->admin_secret ? 'admin' : 'user';

            // Creating the user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $role
            ]);

            $profile = $user->profile()->create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name
            ]);

            $organization = $user->owned_organisations()->create([
                'name' => $request->first_name."'s Organisation",
            ]);

            $organization_user = OrganisationUser::create([
                'user_id' => $user->id,
                'org_id' => $organization->org_id
            ]);

            $roles = $user->roles()->create([
                'name' => $role,
                'org_id' => $organization->org_id
            ]);
            DB::table('users_roles')->insert([
                'user_id' => $user->id,
                'role_id' => $roles->id
            ]);

            // Generate JWT token
            $token = JWTAuth::fromUser($user);

            DB::commit();

            return response()->json([
                'status' => 201,
                "message" => "User Created Successfully",
                'access_token' => $token,
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'first_name' => $request->first_name,
                        'last_name' => $request->last_name,
                        'avatar_url' => $user->profile->avatar_url,
                        'email' => $user->email,
                        'role' => $user->role
                    ]
                ],
            ], 201);
            // return $this->apiResponse('Registration successful', Response::HTTP_CREATED, $data);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->apiResponse('Registration unsuccessful', Response::HTTP_BAD_REQUEST);
        }

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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
