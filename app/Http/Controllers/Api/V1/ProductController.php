<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */

    public function destroy($id)
    {
        $user = Auth::user();
        $product = Product::where('product_id', $id)->first();

        if (!$product) {
            return response()->json([
                'error' => 'Product not found',
                'message' => "The product with ID {$id} does not exist."
            ], 404);
        }

        if ($product->user_id !== $user->id) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'You are not authorized to delete this product.'
            ], 403);
        }

        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully.'
        ], 200);
    }
}
