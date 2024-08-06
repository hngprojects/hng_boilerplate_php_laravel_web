<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AnalyticController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $totalUsers = User::count();
        $totalDeletedUsers = User::onlyTrashed()->count();
        $activeUsers = User::where('is_active', 1)->count() - $totalDeletedUsers;
        $newUsers = User::where('created_at', '>=', Carbon::now()->subMonth())->count();
        $totalRevenue = Order::sum('amount');

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'new_users' => $newUsers,
            'total_revenue' => $totalRevenue,
        ], 200);
    }

    public function getSummary(Request $request) {
        $totalUsers = User::count();
        $totalDeletedUsers = User::onlyTrashed()->count();
        $activeUsers = User::where('is_active', 1)->count() - $totalDeletedUsers;
        $newUsers = User::where('created_at', '>=', Carbon::now()->subMonth())->count();
        $totalRevenue = Order::sum('amount');

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'new_users' => $newUsers,
            'total_revenue' => $totalRevenue,
        ], 200);
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
