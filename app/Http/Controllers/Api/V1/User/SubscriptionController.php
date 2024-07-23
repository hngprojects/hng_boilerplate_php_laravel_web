<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
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
     * cancel user subscription
     */
    public function destroy(Request $request, UserSubscription $userSubscription)
    {
        $request->validate([
            'cancellation_reason' => 'sometimes|string',
        ]);
        try {
            if ($userSubscription->cancelled_at !== null) {
                return response()->json([
                    'status' => 'duplicate',
                    'message' => 'subscription plan has already been cancelled',
                    'status_code' => Response::HTTP_CONFLICT,
                ], Response::HTTP_CONFLICT);
            }

            $userSubscription->cancelled_at = Carbon::now();
            $userSubscription->cancellation_reason = $request->input('cancellation_reason') ?? null;
            $userSubscription->save();
            return response()->json([
                'status' => 'success',
                'message' => 'subscription plan cancelled successfully',
                'status_code' => Response::HTTP_OK,
            ], Response::HTTP_OK);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'plan cancellation failed',
                'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        }
    }
}
