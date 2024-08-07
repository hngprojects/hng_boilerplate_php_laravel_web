<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        $currentMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();
        $userProducts = $user->products();
        $currentMonthRevenue = $userProducts
            ->with(['orders' => function ($query) use ($currentMonth) {
                $query->where('created_at', '>=', $currentMonth);
            }])
            ->get()
            ->flatMap(function ($product) {
                return $product->orders->map(function ($order) {
                    return $order->quantity * $order->amount;
                });
            })->sum();

        $currentMonthOrders = $userProducts->withCount(['orders' => function ($query) use ($currentMonth) {
            $query->where('created_at', '>=', $currentMonth);
        }])
            ->get()
            ->sum('orders_count');

        $lastMonthRevenue = $userProducts
            ->with(['orders' => function ($query) use ($currentMonth, $lastMonth) {
                $query->whereBetween('created_at', [$lastMonth, $currentMonth]);
            }])
            ->get()
            ->flatMap(function ($product) {
                return $product->orders->map(function ($order) {
                    return $order->quantity * $order->amount;
                });
            })->sum();

        $lastMonthOrders = $userProducts->withCount(['orders' => function ($query) use ($currentMonth, $lastMonth) {
            $query->whereBetween('created_at', [$lastMonth, $currentMonth]);
        }])
            ->get()
            ->sum('orders_count');

        $percentageDifference = 0;

        if ($lastMonthRevenue > 0 && $currentMonthRevenue > 0) {
            $percentageDifference = (($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100;
        } else if ($currentMonthRevenue > 0 && $lastMonthRevenue === 0) {
            $percentageDifference = 100;
        }

        $percentageDifferenceOrders = 0;
        if ($lastMonthOrders > 0 && $currentMonthOrders > 0) {
            $percentageDifferenceOrders = (($currentMonthOrders - $lastMonthOrders) / $lastMonthOrders) * 100;
        } else if ($currentMonthOrders > 0 && $lastMonthOrders === 0) {
            $percentageDifferenceOrders = 100;
        }

        $oneHourAgo = now()->subHour();
        $activeUser = $user->owned_organisations()->with(['users' => function ($query) {
            $query->where('is_active', true);
        }])->count();
        $activeUserAnHourAgo = $user->owned_organisations()->with(['users' => function ($query) use ($oneHourAgo) {
            $query->where([
                ['is_active', true],
                ['modified_at', '>=', $oneHourAgo]
                ]
            );
        }])->count();

        return response()->json([
            'message' => 'Dashboard retrieved successfully',
            'status_code' => Response::HTTP_OK,
            'data' => [
                'revenue' => [
                    'current_month' => $currentMonthRevenue,
                    'previous_month' => $lastMonthRevenue,
                    'percentage_difference' => $percentageDifference . '%',
                ],
                'subscriptions' => [
                    'current_month' => 0,
                    'previous_month' => 0,
                    'percentage_difference' => 0 . '%',
                ],
                'orders' => [
                    'current_month' => $currentMonthOrders,
                    'previous_month' => $lastMonthOrders,
                    'percentage_difference' => $percentageDifferenceOrders . '%',
                ],
                'active_users' => [
                    'current' => $activeUser,
                    'difference_an_hour_ago' => max(($activeUser - $activeUserAnHourAgo), 0),
                ],
            ]
        ]);
    }

    public function recent_sales()
    {
        $user = auth()->user();
        $orders = Order::whereHas('product', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with('user')->get();

        return response()->json([
            'message' => 'Recent sales retrieved successfully',
            'status_code' => Response::HTTP_OK,
            'data' => $orders,
        ]);
    }

    public function user_analytics()
    {
        $user = Auth::user();

        $monthlyRevenue = Order::whereHas('product', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->select(
                DB::raw('EXTRACT(MONTH FROM created_at) as month'),
                DB::raw('SUM(total_amount) as total_revenue')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total_revenue', 'month')
            ->toArray();

        $revenueByMonth = collect(range(1, 12))->mapWithKeys(function ($month) use ($monthlyRevenue) {
            $monthName = date('M', mktime(0, 0, 0, $month, 1));
            $revenue = $monthlyRevenue[$month] ?? 0;
            return [$monthName => $revenue];
        });

        return response()->json([
            'message' => 'User analytics retrieved successfully',
            'status_code' => Response::HTTP_OK,
            'data' => [
                $revenueByMonth,
            ]
        ]);
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
