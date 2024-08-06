<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class AnalyticsController extends Controller
{
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

    public function getLineChartData(Request $request) {
        $currentYear = Carbon::now()->year;

        $monthlyRevenue = Order::selectRaw('EXTRACT(MONTH FROM created_at) as month, SUM(total) as revenue')
            ->whereYear('created_at', $currentYear)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('revenue', 'month')
            ->toArray();

        $data = array_fill(1, 12, 0);
        foreach ($monthlyRevenue as $month => $revenue) {
            $data[$month] = $revenue;
        }    

        $labels = [
            'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
            'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
        ];

        return response()->json([
            'status' => true,
            'status_code' => 200,
            'labels' => $labels,
            'data' => array_values($data),
        ], 200);
    }
}
