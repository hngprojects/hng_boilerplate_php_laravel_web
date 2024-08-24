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
                "data" => [
                    "total_users" => $totalUsers,
                    "deleted_users" => $totalDeletedUsers,
                    "active_users" => $totalActiveUsers,
                    "in_active_users" => $totalInActiveUsers,
                ]
            ],
            200
        );
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::paginate(15);
        
        return response()->json([
            'status_code' => 200,
            'message' => 'Users retrieved successfully',
            'status' => 'success',
            'data' => [
                'users' => $users->items(),
                'pagination' => [
                'current_page' => $users->currentPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'last_page' => $users->lastPage(),
            ],
            ]
        ]);
    }
    
    


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }
    public function show(User $user)
    {
        // Load the necessary relationships
        $user->load('profile', 'products', 'organisations');
        
        // Format the response data
        $response = [
            'status_code' => 200,
            'user' => [
                'id' => $user->id,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'first_name' => $user->profile->first_name ?? '', 
                'last_name' => $user->profile->last_name ?? '', 
                'email' => $user->email,
                'status' => null, 
                'phone' => $user->phone,
                'is_active' => $user->is_active,
                'backup_codes' => null, 
                'attempts_left' => null, 
                'time_left' => null,
                'secret' => null, 
                'is_2fa_enabled' => false, 
                'deletedAt' => $user->deleted_at,
                'profile' => [
                    'id' => $user->profile->id ?? null,
                    'created_at' => $user->profile->created_at ?? null,
                    'updated_at' => $user->profile->updated_at ?? null,
                    'username' => '', 
                    'jobTitle' => $user->profile->job_title ?? null,
                    'pronouns' => $user->profile->pronoun ?? null,
                    'department' => null, 
                    'email' => $user->email,
                    'bio' => $user->profile->bio ?? null,
                    'social_links' => null,
                    'language' => null, 
                    'region' => null, 
                    'timezones' => null, 
                    'profile_pic_url' => $user->profile->avatar_url ?? null,
                    'deletedAt' => $user->profile->deleted_at ?? null
                ],
                'owned_organisations' => $user->organisations->map(function ($organisation) {
                    return [
                        'id' => $organisation->id,
                        'created_at' => $organisation->created_at,
                        'updated_at' => $organisation->updated_at,
                        'name' => $organisation->name,
                        'description' => $organisation->description,
                        'email' => $organisation->email,
                        'industry' => $organisation->industry,
                        'type' => $organisation->type,
                        'country' => $organisation->country,
                        'address' => $organisation->address,
                        'state' => $organisation->state,
                        'isDeleted' => $organisation->deleted_at ? true : false
                    ];
                })
            ]
        ];
    
        return response()->json($response);
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
            "phone" => 'nullable|string',
            'pronouns' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'social' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:500',
            'phone_number' => 'nullable|string|max:20',
            'avatar_url' => 'nullable|string|url',
            'recovery_email' => 'nullable|string|email|max:255'
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
    
        $authUser = auth()->user();
    
        if ($authUser->id !== $user->id) {
            if (!in_array($authUser->role, ['superAdmin', 'admin'])) {
                return response()->json([
                    'status_code' => 403,
                    'message' => 'Unauthorized to delete this user'
                ], 403);
            }
        }
    
        $user->delete();
    
        return response()->json([
            'status_code' => 200,
            'message' => 'User deleted successfully'
        ], 200);
    }
    

}
