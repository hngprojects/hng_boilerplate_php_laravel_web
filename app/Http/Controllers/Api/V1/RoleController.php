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
        // Check whether user has admin role
        $user = $request->user();

        // Check if the user has a specific role that grants admin privileges
        $isAdmin = $user->roles()->where('org_id', $org_id)->where('name', 'Admin')->exists();

        if (!$isAdmin) {
            return response()->json([
                'message' => 'Insufficient permission.'
            ], 403);
        }

        // Check whether organisation & role exist
        $organisation = Organisation::find($org_id);
        if (!$organisation) {
            return response()->json([
                'message' => 'Organisation not found.'
            ], 404);
        }

        $role = Role::where('org_id', $org_id)->findOrFail($roleId);
        if (!$role) {
            return response()->json([
                'message' => 'Role not found.'
            ], 404);
        }

        // Disable role
        try {
            DB::beginTransaction();

            $role->is_active = false;
            $role->save();

            // Move all users with the disabled role to the default role
            $defaultRole = Role::where('org_id', $org_id)->where('is_default', true)->first();
            User::whereHas('roles', function ($query) use ($roleId) {
                $query->where('role_id', $roleId);
            })->each(function ($user) use ($defaultRole, $role) {
                $user->roles()->attach($defaultRole->id);
                $user->roles()->detach($role->id);
            });

            DB::commit();

            return response()->json([
                'message' => "Role disabled successfully",
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => "Role disabling failed - " . $e->getMessage(),
            ], 400);
        }
    }
}

?>