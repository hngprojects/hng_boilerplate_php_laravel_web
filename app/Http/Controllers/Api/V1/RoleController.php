<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
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
    public function store(StoreRoleRequest $request, $org_id)
    {
        try {
            DB::beginTransaction();

            // Validate the organisation ID
            if (!is_numeric($org_id)) {
                return response()->json([
                    'status_code' => Response::HTTP_BAD_REQUEST,
                    'error' => 'Invalid input',
                    'message' => 'Invalid organisation ID format',
                ], Response::HTTP_BAD_REQUEST);
            }

            // Check whether the organisation exists
            $organisation = Organisation::find($org_id);
            if (!$organisation) {
                return response()->json([
                    'status_code' => Response::HTTP_NOT_FOUND,
                    'error' => 'Organisation not found',
                    'message' => 'The organisation with ID ' . $org_id . ' does not exist',
                ], Response::HTTP_NOT_FOUND);
            }

            // Check for duplicate role name within the organisation
            $existingRole = Role::where('org_id', $org_id)->where('name', $request->name)->first();
            if ($existingRole) {
                return response()->json([
                    'status_code' => Response::HTTP_CONFLICT,
                    'error' => 'Conflict',
                    'message' => 'A role with this name already exists in the organisation',
                ], Response::HTTP_CONFLICT);
            }

            // Creating the role
            $role = Role::create([
                'name' => $request->name,
                'description' => $request->description,
                'org_id' => $org_id,
            ]);

            DB::commit();

            return response()->json([
                'id' => $role->id,
                'name' => $role->name,
                'description' => $role->description,
                'message' => "Role created successfully",
                'status_code' => Response::HTTP_CREATED,
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Role creation error: ' . $e->getMessage());

            return response()->json([
                'status_code' => Response::HTTP_BAD_REQUEST,
                'error' => 'Invalid input',
                'message' => 'Role creation failed - ' . $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
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

    public function update(UpdateRoleRequest $request, $org_id, $role_id)
    {
        $user = auth('api')->user();
        if(!$user) return ResponseHelper::response("Authentication failed", 401, null);
        if($organisation = Organisation::find($org_id)){
          if(!$organisation->users->contains($user->id)) return ResponseHelper::response("You are not authorised to perform this action", 401, null);
          if($role = Role::find($role_id)){
              $role->update($request->only('name', 'description'));
              return ResponseHelper::response("Role updated successfully", 200, $role);
          } else return ResponseHelper::response("Role not found", 404, null);
        } else return ResponseHelper::response("Organisation not found", 404, null);
    }

    // To get all roles
    public function index($org_id)
    {
        try {
            // Validate the organisation ID
            if (!is_numeric($org_id)) {
                return response()->json([
                    'status_code' => Response::HTTP_BAD_REQUEST,
                    'error' => 'Bad Request',
                    'message' => 'Invalid organisation ID format',
                ], Response::HTTP_BAD_REQUEST);
            }

            // Check whether organisation exists
            $organisation = Organisation::find($org_id);
            if (!$organisation) {
                return response()->json([
                    'status_code' => Response::HTTP_NOT_FOUND,
                    'error' => 'Not Found',
                    'message' => 'The organisation with ID ' . $org_id . ' does not exist',
                ], Response::HTTP_NOT_FOUND);
            }

            // Fetch all roles within the organisation
            $roles = Role::where('org_id', $org_id)->get(['id', 'name', 'description']);

            return response()->json([
                'status_code' => Response::HTTP_OK,
                'data' => $roles,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'error' => 'Internal Server Error',
                'message' => 'An error occurred while fetching roles',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Get a role by id
    public function show($org_id, $role_id)
    {
        try {
            // validate organisation ID and role ID
            if (!is_numeric($org_id) || !is_numeric($role_id)) {
                return response()->json([
                    'status_code' => Response::HTTP_BAD_REQUEST,
                    'error' => 'Bad Request',
                    'message' => 'Invalid organisation ID or role ID format',
                ], Response::HTTP_BAD_REQUEST);
            }

            // Check whether organisation exists
            $organisation = Organisation::find($org_id);
            if (!$organisation) {
                return response()->json([
                    'status_code' => Response::HTTP_NOT_FOUND,
                    'error' => 'Not Found',
                    'message' => 'The organisation with ID ' . $org_id . 'does not exist',
                ], Response::HTTP_NOT_FOUND);
            }

            // Check whether role exists within the organisation
            $role = Role::where('org_id', $org_id)->findOrFail($role_id);

            // Fetch all permissions and format them
            $permissions = $role->permissions->map(function ($permission) use ($role) {
                return [
                    'id' => $permission->id,
                    'category' => $permission->category,
                    'permission_list' => [
                        'can_view_transactions' => $role->permissions()->where('permission_id', $permission->id)->where('name', 'can_view_transactions')->exists(),
                        'can_view_refunds' => $role->permissions()->where('permission_id', $permission->id)->where('name', 'can_view_refunds')->exists(),
                        'can_edit_transactions' => $role->permissions()->where('permission_id', $permission->id)->where('name', 'can_edit_transactions')->exists(),
                    ],
                ];
            });

            return response()->json([
                'status_code' => Response::HTTP_OK,
                'data' => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'description' => $role->description,
                    'permissions' => $permissions, 
                ],
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status_code' => Response::HTTP_NOT_FOUND,
                'error' => 'Not Found',
                'message' => 'The role with ID ' . $role_id . ' does not exist',
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'error' => 'Internal Server Error',
                'message' => 'An error occurred while fetching the role',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
