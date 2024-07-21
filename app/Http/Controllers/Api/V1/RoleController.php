<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
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
}
