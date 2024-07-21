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
            // $user = User::inRandomOrder()->first(); // get a random user, pending authentication module implementation
            $user = auth('api')->user(); // get the authenticated user
            if(!$user) return ResponseHelper::response('Bad request', "Authentication failed", null, 401);
            $validPayload['user_id'] = (string)$user->id;
            DB::beginTransaction();
            try {
                $organisation = Organisation::create($validPayload);
                $organisation->users()->attach((string)$user->id);
                DB::commit();
                return ResponseHelper::response('success', "Organisation created successfully", $organisation->getPublicColumns(), 201);
            }catch (\Exception $e) {
                DB::rollBack();
                return ResponseHelper::response('Bad request', "Client error", null, 400);
            }
        }else{
            return ResponseHelper::response('Bad request', "Client error", null, 400);
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
