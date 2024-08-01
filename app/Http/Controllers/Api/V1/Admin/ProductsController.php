<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ProductsController extends Controller
{
    public function index()
    {
        $products = Product::paginate(10);
        return response()->json([
            'data' => $products->items(),
            'links' => [
                'first' => $products->url(1),
                'last' => $products->url($products->lastPage()),
                'prev' => $products->previousPageUrl(),
                'next' => $products->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $products->currentPage(),
                'from' => $products->firstItem(),
                'last_page' => $products->lastPage(),
                'path' => $products->path(),
                'per_page' => $products->perPage(),
                'to' => $products->lastItem(),
                'total' => $products->total(),
            ],
        ], 200);
    }

    public function store(Request $request)
{
    try {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'imageUrl' => 'required|string',
            'quantity' => 'required|integer|min:0',
            'status' => 'required|in:active,draft',
            'slug' => 'required|string|max:255|unique:products,slug',
            'tags' => 'required|string',
        ]);

        $validatedData['price'] = (int) ($validatedData['price'] * 100);
        $validatedData['user_id'] = auth()->id();

        $product = Product::create($validatedData);

        return response()->json(['data' => array_merge(
            $product->toArray(),
            ['price' => $product->price / 100]
        )], 201);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'An error occurred while creating the product.',
            'error' => $e->getMessage()
        ], 500);
    }
}


public function show($id)
{
    $product = Product::findOrFail($id);
    return response()->json(['data' => $product]);
}

public function destroy(string $productId)
    {
        if (!Auth::check()) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'You must be authenticated to delete a product.'
            ], 401);
        }

        $product = Product::where('product_id', $productId)->first();

        if (!$product) {
            return response()->json([
                'error' => 'Product not found',
                'message' => "The product with ID $productId does not exist."
            ], 404);
        }

        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully.'
        ], 204);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $productId)
{
    try {
        $product = Product::where('product_id', $productId)->firstOrFail();

        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'imageUrl' => 'sometimes|url',
            'quantity' => 'sometimes|integer|min:0',
            'status' => 'sometimes|in:active,inactive',
            'slug' => 'sometimes|string|unique:products,slug,' . $product->product_id . ',product_id',
            'tags' => 'sometimes|string',
        ]);

        $product->update($validatedData);

        return response()->json([
            'message' => 'Product updated successfully.',
            'data' => $product
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'An error occurred while updating the product.',
            'error' => $e->getMessage()
        ], 500);
    }
}



    public function edit(string $product_id)
    {
        if (!Auth::check()) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'You must be authenticated to access the product edit details.'
            ], 401);
        }

        $product = Product::where('product_id', $product_id)->first();

        if (!$product) {
            return response()->json([
                'error' => 'Product not found',
                'message' => "The product with ID $product_id does not exist."
            ], 404);
        }

        return response()->json([
            'data' => $product
        ], 200);
    }


    public function totalRevenue()
    {
        $totalRevenue = Product::sum('price') / 100;
        return response()->json(['total_revenue' => $totalRevenue], 200);
    }

    public function totalPrice()
    {
        $totalPrice = Product::sum('price') / 100;
        return response()->json(['total_price' => $totalPrice], 200);
    }
}
