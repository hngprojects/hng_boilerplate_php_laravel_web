<?php

namespace App\Http\Controllers;

use App\Models\BillingPlan;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BillingPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $plans = BillingPlan::get(['id', 'name', 'price']);

            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Pricing plans retrieved successfully',
                'data' => $plans,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve job listings.',
                'error' => $e->getMessage(),
                'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
    public function store(Request $request)
    {
        //
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
