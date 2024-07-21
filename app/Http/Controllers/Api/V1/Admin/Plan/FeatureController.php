<?php

namespace App\Http\Controllers\Api\V1\Admin\Plan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Plan\FeatureRequest;
use App\Models\Feature;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class FeatureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FeatureRequest $request)
    {
        try {
            $feature = Feature::create($request->validated());
            return response()->json([
                'data' => $feature,
                'status_code' => Response::HTTP_CREATED,
                'message' => ucfirst('feature created successfully')
            ], Response::HTTP_CREATED);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return \response()->json([
                'error' => ucfirst('feature creation failed'),
                'status_code' => Response::HTTP_BAD_REQUEST
            ]);
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
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
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
