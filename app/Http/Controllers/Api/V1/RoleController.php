<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\User;
use App\Models\Organisation;
use App\Models\Permission;
use App\Models\Role;
use App\Traits\HttpResponses;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    use HttpResponses;
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request)
    {
        Log::info('Incoming role creation request', $request->all());

        try {
            DB::beginTransaction();

            $org_id = $request->organisation_id;

            // Validate the organisation ID as a UUID
            if (!preg_match('/^[a-f0-9-]{36}$/', $org_id)) {
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
            $existingRole = Role::where('org_id', $org_id)->where('name', $request->role_name)->first();
            if ($existingRole) {
                return response()->json([
                    'status_code' => Response::HTTP_CONFLICT,
                    'error' => 'Conflict',
                    'message' => 'A role with this name already exists in the organisation',
                ], Response::HTTP_CONFLICT);
            }

            // Creating the role
            $role = Role::create([
                'name' => $request->role_name,
                'description' => $request->description,
                'org_id' => $org_id,
            ]);

            // Attach the permission to the role
            $role->permissions()->attach($request->permissions_id);

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

    public function assignRole(Request $request, $org_id, $user_id) { 
      $validator = Validator::make($request->all(), [
        'role' => 'required|string|exists:roles,name',
      ]);

      if ($validator->fails()) {
        return $this->apiResponse($validator->errors(), 422);
      }

      $user = User::find($user_id);
      if (!$user) return ResponseHelper::response("User not found", 404, null);
      if($organisation = Organisation::find($org_id)){
        if(!$organisation->users->contains($user->id))
          return ResponseHelper::response("You are not authorised to perform this action", 409, null);

        $role_id = Role::where('org_id', $org_id)->where('name', $request->role)->pluck('id');
        if($result = $user->roles()->syncWithoutDetaching($role_id))
          return ResponseHelper::response("Roles updated successfully", 200, null);
        else 
          return response()->json(['message' => 'Role update failed', 'error' => $result], 400);
        } else return ResponseHelper::response("Organisation not found", 404, null);
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
           // Validate the organisation ID (UUID format)
           if (!Str::isUuid($org_id)) {
               return response()->json([
                   'status_code' => Response::HTTP_BAD_REQUEST,
                   'error' => 'Bad Request',
                   'message' => 'Invalid organisation ID format',
               ], Response::HTTP_BAD_REQUEST);
           }

           // Check whether organisation exists
           $organisation = Organisation::where('org_id', $org_id)->first();
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
            Log::error('Role creation error: ' . $e->getMessage());
           return response()->json([
               'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR,
               'error' => 'Internal Server Error',
               'message' => 'An error occurred while fetching roles',
           ], Response::HTTP_INTERNAL_SERVER_ERROR);
       }
   }

   public function show($org_id, $role_id)
    {
        try {
            // Validate UUID format
            if (!Str::isUuid($org_id) || !is_numeric($role_id)) {
                Log::error('Invalid UUID or ID format', [
                    'org_id' => $org_id,
                    'role_id' => $role_id,
                    'org_id_type' => gettype($org_id),
                    'role_id_type' => gettype($role_id)
                ]);

                return response()->json([
                    'status_code' => Response::HTTP_BAD_REQUEST,
                    'error' => 'Bad Request',
                    'message' => 'Invalid organisation ID or role ID format',
                ], Response::HTTP_BAD_REQUEST);
            }

            // Check if the organisation exists
            $organisation = Organisation::where('org_id', $org_id)->first();
            if (!$organisation) {
                Log::error('Organisation not found', ['org_id' => $org_id]);

                return response()->json([
                    'status_code' => Response::HTTP_NOT_FOUND,
                    'error' => 'Not Found',
                    'message' => 'The organisation with ID ' . $org_id . ' does not exist',
                ], Response::HTTP_NOT_FOUND);
            }

            // Check if the role exists within the organisation
            $role = Role::where('org_id', $org_id)->where('id', $role_id)->first();
            if (!$role) {
                Log::error('Role not found', ['org_id' => $org_id, 'role_id' => $role_id]);

                return response()->json([
                    'status_code' => Response::HTTP_NOT_FOUND,
                    'error' => 'Not Found',
                    'message' => 'The role with ID ' . $role_id . ' does not exist',
                ], Response::HTTP_NOT_FOUND);
            }

            // Fetch and format permissions
            $permissions = $role->permissions->map(function ($permission) use ($role) {
                return [
                    'id' => $permission->id,
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
        } catch (\InvalidArgumentException $e) {
            Log::error('Invalid argument exception', [
                'exception' => $e->getMessage(),
                'org_id' => $org_id,
                'role_id' => $role_id
            ]);

            return response()->json([
                'status_code' => Response::HTTP_BAD_REQUEST,
                'error' => 'Bad Request',
                'message' => 'Invalid organisation ID or role ID format',
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Model not found exception', [
                'exception' => $e->getMessage(),
                'org_id' => $org_id,
                'role_id' => $role_id
            ]);

            return response()->json([
                'status_code' => Response::HTTP_NOT_FOUND,
                'error' => 'Not Found',
                'message' => 'The role with ID ' . $role_id . ' does not exist',
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            Log::error('General exception', [
                'exception' => $e->getMessage(),
                'org_id' => $org_id,
                'role_id' => $role_id
            ]);

            return response()->json([
                'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'error' => 'Internal Server Error',
                'message' => 'An error occurred while fetching the role',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function assignPermissions(Request $request, $org_id, $role_id){
      $role = Role::where('org_id', $org_id)->with('permissions')->find($role_id);
      $payload = Validator::make($request->all(), [
        'permission_list' => 'required|array'
      ]);
      if($payload->fails()) return ResponseHelper::response($payload->errors(), 422, null);
      if($role && !$payload->fails()){
        foreach ($request->permission_list as $permission => $value) {
          $permissionId = Permission::where('name', $permission)->value('id');
          if ($value && $permissionId) {
            $role->permissions()->attach($permissionId);
          } else {
            $role->permissions()->detach($permissionId);
          }
        }
        return ResponseHelper::response("Permissions updated successfully", 202, null);
      } else return ResponseHelper::response("Role not found", 404, null);
    }

}

?>