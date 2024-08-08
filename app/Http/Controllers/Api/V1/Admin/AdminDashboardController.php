<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Response;

class AdminDashboardController extends Controller
{
    public function getStatistics()
    {
        $currentMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();

        // Revenue statistics
        $currentMonthRevenue = Order::where('created_at', '>=', $currentMonth)
            ->sum('total_amount');
        $lastMonthRevenue = Order::whereBetween('created_at', [$lastMonth, $currentMonth])
            ->sum('total_amount');
        $revenuePercentageDifference = $this->calculatePercentageDifference($currentMonthRevenue, $lastMonthRevenue);

        // User statistics
        $totalUsers = User::count();
        $lastMonthUsers = User::where('created_at', '<', $currentMonth)->count();
        $userPercentageDifference = $this->calculatePercentageDifference($totalUsers, $lastMonthUsers);

        // Product statistics
        $totalProducts = Product::count();
        $lastMonthProducts = Product::where('created_at', '<', $currentMonth)->count();
        $productPercentageDifference = $this->calculatePercentageDifference($totalProducts, $lastMonthProducts);

        // Lifetime sales statistics
        $lifetimeSales = Order::sum('total_amount');
        $lastMonthSales = Order::where('created_at', '<', $currentMonth)->sum('total_amount');
        $salesPercentageDifference = $this->calculatePercentageDifference($lifetimeSales, $lastMonthSales);

        return response()->json([
            'message' => 'Dashboard statistics retrieved successfully',
            'status_code' => Response::HTTP_OK,
            'data' => [
                'total_revenue' => [
                    'current_month' => $currentMonthRevenue,
                    'previous_month' => $lastMonthRevenue,
                    'percentage_difference' => round($revenuePercentageDifference, 2) . '%',
                ],
                'total_users' => [
                    'current_month' => $totalUsers,
                    'previous_month' => $lastMonthUsers,
                    'percentage_difference' => round($userPercentageDifference, 2) . '%',
                ],
                'total_products' => [
                    'current_month' => $totalProducts,
                    'previous_month' => $lastMonthProducts,
                    'percentage_difference' => round($productPercentageDifference, 2) . '%',
                ],
                'lifetime_sales' => [
                    'current_month' => $lifetimeSales,
                    'previous_month' => $lastMonthSales,
                    'percentage_difference' => round($salesPercentageDifference, 2) . '%',
                ],
            ]
        ]);
        
    }

    private function calculatePercentageDifference($current, $previous)
    {
        if ($previous > 0 && $current > 0) {
            return (($current - $previous) / $previous) * 100;
        } elseif ($current > 0 && $previous === 0) {
            return 100;
        }
        return 0;
    }
}
