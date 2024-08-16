<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use App\Models\Organisation;
use App\Models\OrganisationUser;
use App\Services\OrganisationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    use HttpResponses;

    public function __construct(public OrganisationService $organisationService)
    {    
    }
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
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email:rfc|max:255|unique:users',
            'password' => 'required|string|min:6',
            'invite_token' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponseAlign($validator->errors());
        }

        try {
            DB::beginTransaction();

            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'user'
            ]);

            $user->profile()->create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name
            ]);

            $name = $request->first_name."'s Organisation";
            $organisation = $this->organisationService->create($user, $name);

            $roles = $user->roles()->create([
                'name' => 'admin',
                'org_id' => $organisation->org_id
            ]);
            DB::table('users_roles')->insert([
                'user_id' => $user->id,
                'role_id' => $roles->id
            ]);

            // Generate JWT token
            $token = JWTAuth::fromUser($user);

            DB::commit();

            $email_template_id = null;

            $emailTemplate = EmailTemplate::where('title', 'welcome-email')->first();;
            if ($emailTemplate) {
                $email_template_id = $emailTemplate->id;
            }

            return response()->json([
                'status_code' => 201,       
                "message" => "User Created Successfully",
                'email_template_id' => $email_template_id,
                'access_token' => $token,
                'data' => [
                    'user' => new UserResource($user->load('owned_organisations', 'profile'))
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return $this->ap('Registration unsuccessful', Response::HTTP_BAD_REQUEST);
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
