<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Organisation;
use App\Services\OrganisationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;
use App\Models\EmailTemplate;

class AuthController extends Controller
{
    public function __construct(public OrganisationService $organisationService)
    {
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email:rfc|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status_code' => 422,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }
    
        try {
            DB::beginTransaction();
    
            $user = User::create([
                'id' => Str::uuid(),
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'user',
                'is_verified' => 1,
            ]);
    
            $user->profile()->create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
            ]);
    
            $name = $request->first_name . "'s Organisation";
            $organisation = $this->organisationService->create($user, $name);
    
            $role = $user->roles()->create([
                'name' => 'admin',
                'org_id' => $organisation->org_id,
            ]);
    
            DB::table('users_roles')->insert([
                'user_id' => $user->id,
                'role_id' => $role->id,
            ]);
    
            $token = JWTAuth::fromUser($user);
    
            $is_superadmin = in_array($user->role, ['admin']);
    
            DB::commit();

            $email_template_id = null;

            $emailTemplate = EmailTemplate::where('title', 'welcome-email')->first();;
            if ($emailTemplate) {
                $email_template_id = $emailTemplate->id;
            }
    
            return response()->json([
                'status_code' => 201,
                'message' => 'User Created Successfully',
                'email_template_id' => $email_template_id,
                'access_token' => $token,
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'first_name' => $user->profile->first_name,
                        'last_name' => $user->profile->last_name,
                        'email' => $user->email,
                        'avatar_url' => $user->avatar_url,
                        'is_superadmin' => $is_superadmin,
                        'role' => $user->role,
                    ],
                    'organisations' => [
                        [
                            'organisation_id' => $organisation->org_id,
                            'name' => $organisation->name,
                            'user_role' => 'admin',
                            'is_owner' => true,
                        ]
                    ]
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status_code' => 500,
                'message' => 'Registration unsuccessful: ' . $e->getMessage(),
            ], 500);
        }
    }
    
}
