<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProductRequest;

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
    // public function store(CreateProductRequest $request)
    // {
    //     $request->validated();

    //     $user = auth()->user();

    //     $product = $user->products()->create($request->all());

    //     return response()->json([
    //         'message' => 'Product created successfully',
    //         'status_code' => 201,
    //         'data' => [
    //             'product_id' => $product->product_id,
    //             'name' => $product->name,
    //             'description' => $product->description,
    //         ]
    //     ], 201);
    // }

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
        $validate = $request->validate([
            'name' => 'sometimes|string',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric',
            'tag' => 'sometimes|string'
        ]);

        if (!$validate) {
            return response()->json([
                'status_code' => 400,
                'message' => $validate,
                'error' => 'Bad Request'
            ], 400);
        }

        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found',
                'error' => 'Not Found',
                'status_code' => 404
            ], 404);
        }

        // Update product attributes
        if (isset($validate['name'])) {
            $product->name = $validate['name'];
        }
        if (isset($validate['description'])) {
            $product->description = $validate['description'];
        }

        if (isset($validate['price'])) {
            $product->description = $validate['price'];
        }

        if (isset($validate['tag'])) {
            $product->description = $validate['tag'];
        }


        $product->updated_at = now(); // Update timestamp

        // Save the updated product
        $product->save();

        // Return response with updated product details
        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'description' => $product->description,
            'tag' => $product->tag,
            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
