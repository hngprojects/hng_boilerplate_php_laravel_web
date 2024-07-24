<?php

namespace App\Http\Controllers\Api\V1\Organisation;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrganisationRequest;
use App\Models\Organisation;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\OrganisationResource;

class OrganisationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $user = auth('api')->user();
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized',
                    'status_code' => 401
                ], 401);
            }

            $organisations = $user->organisations;

            if ($organisations->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No organisations available',
                    'data' => [
                        'organisations' => []
                    ]
                ], 200);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Organizations retrieved successfully',
                'status_code' => 200,
                'data' => [
                    'organisations' => OrganisationResource::collection($organisations)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred',
                'status_code' => 500,
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrganisationRequest $request)
    {
        if($validPayload = $request->validated()){
            $user = auth()->user();
            if(!$user){
                return ResponseHelper::response("Authentication failed", 401, null);
            }
            $validPayload['user_id'] = $user->id;
            DB::beginTransaction();
            try {
                $organisation = Organisation::create($validPayload);
                $organisation->users()->attach($user->id);
                DB::commit();
                return ResponseHelper::response("Organisation created successfully", 201, $organisation->getPublicColumns());
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                DB::rollBack();
                return ResponseHelper::response("Client error", 400, null);
            }
        }else{
            return ResponseHelper::response("Client error", 400, null);
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
    public function update(StoreOrganisationRequest $request, $org_id)
    {
        if($validPayload = $request->validated()){
            $user = auth('api')->user();
            if(!$user) return ResponseHelper::response("Authentication failed", 401, null);
            $organisation = Organisation::find($org_id);
            if(!$organisation) return ResponseHelper::response("Organisation not found", 404, null);
            if(!$organisation->users->contains($user->id)) return ResponseHelper::response("You are not authorised to perform this action", 403, null);
            try {
                unset($validPayload['email']);
                $organisation->update($validPayload);
                return ResponseHelper::response("Organisation updated successfully", 200, $organisation->getPublicColumns());
            }catch (\Exception $e) {
                return ResponseHelper::response("Client error", 400, null);
            }
        }else{
            return ResponseHelper::response("Client error", 400, null);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function removeUser(Request $request, $org_id, $user_id)
    {
        $organization = Organisation::findOrFail($org_id);

        // Use $request->auth instead of Auth::user()
        if (!$request->user()->can('removeUser', $organization)) {
            return response()->json([
                'status' => 'Forbidden',
                'message' => 'Only admin can remove users',
                'status_code' => 403
            ], 403);
        }

        $user = User::find($user_id);

        if (!$user || !$organization->users()->detach($user)) {
            return response()->json([
                'status' => 'forbidden',
                'message' => 'user not found',
                'status_code' => 404
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'user deleted successfully',
            'status_code' => 200
        ], 200);
    }
}
