<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Models\User;
use App\Models\Organisation;
use App\Models\Role;
use App\Traits\HttpResponses;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    use HttpResponses;
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request)
    {
        try {
            DB::beginTransaction();

            // creating the role 
            $role = Role::create([
                'name' => $request->role_name,
                'org_id' => $request->organisation_id,
            ]);

            $role->permissions()->attach($request->permissions_id);
            DB::commit();

            $code = Response::HTTP_CREATED;

            return response()->json([
                'message' => "Role created successfully",
                'status_code' => $code,
            ], $code);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Role creation error: ' . $e->getMessage());
            $code = Response::HTTP_BAD_REQUEST;
            return response()->json([
                'message' => "Role creation failed - ".$e->getMessage(),
                'status_code' => $code,
            ], $code);
        }
    }

    public function disableRole(StoreRoleRequest $request, $org_id, $roleId)
    {
        // check whether user is admin
        $user = $request->user();
        Log::info('Disabling role request by user:', ['user_id' => $user->id, 'org_id' => $org_id]);

        if (!$user->isAdmin($org_id)) {
            Log::warning('User does not have admin permissions.', ['user_id' => $user->id, 'org_id' => $org_id]);
            return response()->json([
                'message' => 'Insufficient permission.'
            ], 403);
        }

        // check whether organisation & role exists
        $organisation = Organisation::find($org_id);
        if (!$organisation) {
            Log::warning('Organisation not found.', ['org_id' => $org_id]);
            return response()->json([
                'message' => 'Organisation not found.'
            ], 404);
        }

        $role = Role::where('org_id', $org_id)->findOrFail($roleId);
        if (!$role) {
            Log::warning('Role not found.', ['role_id' => $roleId]);
            return response()->json([
                'message' => 'Role not found.'
            ], 404);
        }

        // disable role
        try {
            DB::beginTransaction();

            $role->is_active = false;
            $role->save();

            // move all users with disabled role to default role
            $defaultRole = Role::where('organisation_id', $org_id)->where('is_default', true)->first();
            User::whereHas('roles', function ($query) use ($roleId) {
                $query->where('role_id', $roleId);
            })->each(function ($user) use ($defaultRole, $role) {
                $user->roles()->attach($defaultRole->id);
                $user->roles()->detach($role->id);
            });

            DB::commit();
            
            Log::info('Role disabled successfully.', ['role_id' => $roleId]);
            return response()->json([
                'message' => "Role disabled successfully",
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Role disabling error: ' . $e->getMessage());
            return response()->json([
                'message' => "Role disabling failed - ".$e->getMessage(),
            ], 400);
        }
    }

}
