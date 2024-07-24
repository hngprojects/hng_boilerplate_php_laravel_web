<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProductRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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
    public function store(CreateProductRequest $request)
    {
        $request->validated();

        $user = auth()->user();
        $request['slug'] = Str::slug($request['name']);
        $request['tags'] = " ";
        $request['imageUrl'] = " ";
        $product = $user->products()->create($request->all());

        return response()->json([
            'message' => 'Product created successfully',
            'status_code' => 201,
            'data' => [
                'product_id' => $product->product_id,
                'name' => $product->name,
                'description' => $product->description,
            ]
        ], 201);
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
        if (!Auth::check()) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'You must be authenticated to delete a product.'
            ], 401);
        }
        // Define validation rules
        $rules = [
            'name' => 'sometimes|string',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric',
            'tags' => 'sometimes|string',
            'imageUrl' => 'sometimes|string',
            'slug' => 'sometimes|string'
        ];

        // Validate the request data
        $validator = Validator::make($request->all(), $rules);

        // Check for validation errors
        if ($validator->fails()) {
            return response()->json([
                'status_code' => 422,
                'message' => $validator->errors()->all(),
                'error' => 'Validation fails'
            ], 422);
        }

        $validate = $validator->validated();

        $product = Product::where('product_id', $id)->first();

        if (!$product) {
            return response()->json([
                'message' => 'Product not found',
                'error' => 'Not Found',
                'status_code' => 404
            ], 404);
        }

        // Update product attributes dynamically
        foreach ($validate as $key => $value) {
            $product->{$key} = $value;
        }

        $product->updated_at = now(); // Update timestamp

        // Save the updated product
        $product->save();

        // Return response with updated product details
        return response()->json([
            'id' => $product->product_id,
            'name' => $product->name,
            'price' => $product->price,
            'description' => $product->description,
            'tag' => $product->tags,
            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at,
        ], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($productId)
    {
        if (!Auth::check()) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'You must be authenticated to delete a product.'
            ], 401);
        }

        $product = Product::find($productId);

        if (!$product) {
            return response()->json([
                'error' => 'Product not found',
                'message' => "The product with ID $productId does not exist."
            ], 404);
        }

        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully.'
        ], 200);
    }
}
