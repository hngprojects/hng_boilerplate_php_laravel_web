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

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email:rfc|max:255|unique:users',
            'password' => 'required|string|min:6',
            'invite_token' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(message: $validator->errors(), status_code: 400);
        }

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'user'
            ]);

            $profile = $user->profile()->create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name
            ]);

            $organisations = [];

            if ($request->invite_token) {
                // Handle invite logic here
                // For now, we'll create a default org
                $organization = $this->createDefaultOrganization($user);
                $organisations[] = $this->formatOrganisation($organization, 'admin', true);
            } else {
                $organization = $this->createDefaultOrganization($user);
                $organisations[] = $this->formatOrganisation($organization, 'admin', true);
            }

            $token = JWTAuth::fromUser($user);

            DB::commit();

            return response()->json([
                'status_code' => 201,
                'message' => 'User Created Successfully',
                'access_token' => $token,
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'first_name' => $request->first_name,
                        'last_name' => $request->last_name,
                        'avatar_url' => $user->profile->avatar_url,
                        'email' => $user->email,
                        'is_superadmin' => false,
                        'role' => $user->role
                    ],
                    'organisations' => $organisations
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->apiResponse('Registration unsuccessful', Response::HTTP_BAD_REQUEST);
        }
    }

    private function createDefaultOrganization($user)
    {
        $organization = $user->owned_organisations()->create([
            'name' => $user->profile->first_name . "'s Organisation",
        ]);

        OrganisationUser::create([
            'user_id' => $user->id,
            'org_id' => $organization->org_id
        ]);

        $role = $user->roles()->create([
            'name' => 'admin',
            'org_id' => $organization->org_id
        ]);

        DB::table('users_roles')->insert([
            'user_id' => $user->id,
            'role_id' => $role->id
        ]);

        return $organization;
    }

    private function formatOrganisation($organization, $role, $isOwner)
    {
        return [
            'organisation_id' => $organization->org_id,
            'name' => $organization->name,
            'role' => $role,
            'is_owner' => $isOwner,
        ];
    }
}
