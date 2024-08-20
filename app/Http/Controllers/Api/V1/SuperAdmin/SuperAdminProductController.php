<?php

namespace App\Http\Controllers\Api\V1\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SuperAdminProductController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'imageUrl' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'status_code' => 422,
                'message' => 'Validation errors',
                'data' => $validator->errors(),
            ], 422);
        }

        $user = auth()->user();

        // Fetch the org_id associated with the authenticated user
        $organisation = $user->organisations()->first();
        if (!$organisation) {
            return response()->json([
                'success' => false,
                'status_code' => 400,
                'message' => 'User is not associated with any organisation',
            ], 400);
        }
        $org_id = $organisation->org_id;

        $slug = Str::slug($request->name . '-' . Str::random(8));
        $tags = 'default-tag';

        $product = Product::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image_url' => $request->imageUrl,
            'status' => 'in stock',
            'quantity' => $request->quantity,
            'category' => $request->category,

            'is_archived' => false,
            'org_id' => $org_id,
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
            'name',
            'description',
            'price',
            'slug',
            'tags',
            'imageUrl',
            'status',
            'quantity',
            'org_id'
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

    public function destroy($productId)
    {
        $product = Product::findOrFail($productId);
        $product->delete();

        return response()->json([
            'success' => true,
            'status_code' => 200,
            'message' => 'Product deleted successfully',
        ]);
    }
}
