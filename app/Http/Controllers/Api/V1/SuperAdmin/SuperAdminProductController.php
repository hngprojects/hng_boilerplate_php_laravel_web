<?php

namespace App\Http\Controllers\Api\V1\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;

class SuperAdminProductController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'slug' => 'required|string|max:255',
            'tags' => 'required|string',
            'imageUrl' => 'nullable|string|max:255',
            'status' => 'required|string|max:50',
            'quantity' => 'required|integer',
            'org_id' => 'required|uuid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'status_code' => 422,
                'message' => 'Validation errors',
                'data' => $validator->errors(),
            ], 422);
        }

        $product = Product::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'slug' => $request->slug,
            'tags' => $request->tags,
            'imageUrl' => $request->imageUrl,
            'status' => $request->status,
            'quantity' => $request->quantity,
            'is_archived' => false,
            'org_id' => $request->org_id,
            'category' => $request->category,
        ]);

        return response()->json([
            'success' => true,
            'status_code' => 201,
            'message' => 'Product created successfully',
            'data' => $product,
        ], 201);
    }
    public function update(Request $request, $productId)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric',
            'slug' => 'sometimes|string|max:255',
            'tags' => 'sometimes|string',
            'imageUrl' => 'nullable|string|max:255',
            'status' => 'sometimes|string|max:50',
            'quantity' => 'sometimes|integer',
            'org_id' => 'sometimes|uuid',
            'category' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'status_code' => 422,
                'message' => 'Validation errors',
                'data' => $validator->errors(),
            ], 422);
        }

        $product = Product::findOrFail($productId);

        $product->fill($request->only([
            'name', 'description', 'price', 'slug', 'tags',
            'imageUrl', 'status', 'quantity', 'org_id'
        ]));


        $product->user_id = $product->user_id;

        $product->save();

        return response()->json([
            'success' => true,
            'status_code' => 200,
            'message' => 'Product updated successfully',
            'data' => $product,
        ]);
    }
}
