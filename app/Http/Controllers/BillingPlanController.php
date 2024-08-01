<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
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
            $plans = SubscriptionPlan::select(['id', 'name', 'price', 'created_at'])->get();
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Billing plans retrieved successfully',
                'data' => $plans,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal server error',
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR
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
    public function getBillingPlan($id)
    {


        // Validate the id parameter
        if (!is_string($id) || empty($id)) {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid billing plan ID'
            ], 400);
        }

        try {
            // Retrieve the billing plan by ID
            $billingPlan = SubscriptionPlan::find($id);

            if (!$billingPlan) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Billing plan not found'
                ], 404);
            }

            // Return the billing plan details
            return response()->json([
                'status' => 200,
                'message' => 'Billing plans retrieved successfully',
                'data' => [
                    'id' => $billingPlan->id,
                    'name' => $billingPlan->name,
                    'price' => $billingPlan->price,
                ]
            ], 200);
        } catch (\Exception $e) {
            // Handle unexpected errors
            return response()->json([
                'status' => 500,
                'message' => 'Internal server error'
            ], 500);
        }
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
