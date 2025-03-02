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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'frequency' => 'required|string|in:Monthly,Yearly',
            'is_active' => 'boolean',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
        ]);

        try {
            $billingPlan = SubscriptionPlan::where('name', '=', $validated['name'])->first();

            if($billingPlan){
                return response()->json([
                    "status" => "error",
                    "status_code" => 403,
                    "message" => "Billing plan already exists!",
                    "data" => []
                ], 403);
            }

            $billingPlan = new SubscriptionPlan;
            $billingPlan->name = $validated['name'];
            $billingPlan->duration = $validated['frequency'];
            $billingPlan->price = $validated['amount'];
            $billingPlan->description = $validated['description'];
            $billingPlan->save();

            return response()->json([
                "status" => "success",
                "status_code" => 201,
                "message" => "Billing plan successfully created!",
                "data" => [
                    ...$billingPlan->toArray()
                ]
            ], 201);

        } catch (\Throwable $th) {
            print_r($th);
                return response()->json([
                    "status" => "error",
                    "status_code" => 500,
                    "message" => "Internal Server Error!",
                    "data" => []
                ], 500);
        }
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
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'frequency' => 'required|string|in:Monthly,Yearly',
            'is_active' => 'boolean',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
        ]);

        try {
            $billingPlan = SubscriptionPlan::find($id);
            // Check if the billing plan exists
            if (!$billingPlan) {
                return response()->json([
                    'data' => null,
                    'error' => 'Billing plan not found',
                    'message' => 'No billing plan found with the specified ID',
                    'status_code' => 404
                ], 404);
            }

            // Update the billing plan with the new data
            $billingPlan->name = $validatedData['name'];
            $billingPlan->duration = $validatedData['frequency'];
            $billingPlan->price = $validatedData['amount'];
            $billingPlan->description = $validatedData['description'];
            $billingPlan->save(); // Save the changes

            // Return a success response
            return response()->json([
                'data' => [
                    'id' => $billingPlan->id,
                    'name' => $billingPlan->name,
                    'frequency' => $billingPlan->duration,
                    'amount' => $billingPlan->price,
                    'description' => $billingPlan->description,
                    'created_at' => $billingPlan->created_at,
                    'updated_at' => $billingPlan->updated_at,
                ],
                'message' => 'Billing plan updated successfully',
                'status_code' => 200
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'data' => null,
                'error' => 'Internal server error',
                'message' => 'An error occurred while updating the billing plan',
                'status_code' => 500
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $plans = SubscriptionPlan::find($id);

        if (!$plans) {
            return response()->json([
                'status_code' => 404,
                'status' => 'error',
                'message' => 'Plan not found',
                'data' => []
            ], 404);
        }

        try {
            $plans->delete();

            // Return success response
            return response()->json([
                'data' => [],
                'status_code' => 204,
                'status' => 'success',
                'message' => 'Plan deleted successfully'
            ], 204);
        } catch (\Exception $e) {
            return response()->json([
                'status' => "error",
                'status_code' => 500,
                'message' => 'Internal server error',
                'data' => []
            ], 500);
        }
    }
}
