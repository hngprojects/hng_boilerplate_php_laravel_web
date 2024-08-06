<?php

namespace App\Http\Controllers\Api\V1\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class SuperAdminProductController extends Controller
{
    public function index(Request $request)
    {

        $request->validate([
            'page' => 'integer|min:1',
            'per_page' => 'integer|min:1|max:100'
        ]);


        $paginator = Product::with('user')
            ->where('is_archived', false)
            ->paginate($request->get('per_page', 15));

        $productsCollection = collect($paginator->items());

        $transformedProducts = $productsCollection->map(function ($product) {

            $totalRevenue = DB::table('products')
                ->where('product_id', $product->product_id)
                ->sum(DB::raw('CAST(quantity AS numeric) * price'));


            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;
            $revenuePerMonth = DB::table('products')
                ->where('product_id', $product->product_id)
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->sum(DB::raw('CAST(quantity AS numeric) * price'));

            return [
                'name' => $product->name,
                'username' => $product->user->name,
                'quantity' => $product->quantity,
                'date_added' => $product->created_at,
                'in_stock' => $product->quantity > 0,
                'out_of_stock' => $product->quantity < 1,
                'total_revenue_total' => $totalRevenue,
                'revenue_per_month' => $revenuePerMonth,
            ];
        });

        return response()->json([
            'data' => $transformedProducts,
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total()
        ]);
    }
}
