<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Region;

class RegionController extends Controller
{


    public function index()
    {

        $regions = Region::all();

        return response()->json([
            'status' => "success",
            'status_code' => 200,
            'data' => $regions
        ], 200);
    }


    public function store (Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $errorMessage = implode(" ", $errors);

            return response()->json([
                'status' => "error",
                'status_code' => 422,
                'message' => $errorMessage
            ], 422);
        }


        try {

            $region = Region::create(['name' => $request->name]);

            return response()->json([
                'status' => "success",
                'status_code' => 201,
                'message' => 'Region created successfully.',
                'data' => $region
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => "error",
                'status_code' => 400,
                'message' => 'Failed to submit your request',
            ], 400);
        }

    }


}
