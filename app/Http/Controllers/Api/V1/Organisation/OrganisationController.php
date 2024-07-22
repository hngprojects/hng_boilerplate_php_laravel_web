<?php

namespace App\Http\Controllers\Api\V1\Organisation;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrganisationRequest;
use App\Models\Organisation;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrganisationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrganisationRequest $request)
    {
        if($validPayload = $request->validated()){
            $user = auth('api')->user();
            if(!$user) return ResponseHelper::response("Authentication failed", 401, null);
            // $validPayload['user_id'] = (string)$user->id;
            DB::beginTransaction();
            try {
                $organisation = Organisation::create($validPayload);
                $organisation->users()->attach((string)$user->id);
                DB::commit();
                return ResponseHelper::response("Organisation created successfully", 201, $organisation->getPublicColumns());
            }catch (\Exception $e) {
                DB::rollBack();
                return ResponseHelper::response("Client error".$e, 400, null);
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
