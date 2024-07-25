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
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'minPrice' => 'nullable|numeric|min:0',
            'maxPrice' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->messages() as $field => $messages) {
                foreach ($messages as $message) {
                    $errors[] = [
                        'parameter' => $field,
                        'message' => $message,
                    ];
                }
            }

            return response()->json([
                'success' => false,
                'errors' => $errors,
                'statusCode' => 422
            ], 422);
        }

        $query = Product::query();

        $query->where('name', 'LIKE', '%' . $request->name . '%');

        // Add category filter if provided
        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        if ($request->filled('minPrice')) {
            $query->where('price', '>=', $request->minPrice);
        }

        if ($request->filled('maxPrice')) {
            $query->where('price', '<=', $request->maxPrice);
        }

        $products = $query->get();

        return response()->json([
            'success' => true,
            'products' => $products,
            'statusCode' => 200
        ], 200);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            // Validate pagination parameters
            $request->validate([
                'page' => 'integer|min:1',
                'limit' => 'integer|min:1',
            ]);

            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);

            // Calculate offset
            $offset = ($page - 1) * $limit;

            $products = Product::select('name', 'price')
                ->offset($offset)
                ->limit($limit)
                ->get();

            // Get total product count
            $totalItems = Product::count();
            $totalPages = ceil($totalItems / $limit);

            return response()->json([
                'success' => true,
                'message' => 'Products retrieved successfully',
                'products' => $products,
                'pagination' => [
                    'totalItems' => $totalItems,
                    'totalPages' => $totalPages,
                    'currentPage' => $page,
                ],
                'status_code' => 200,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'bad request',
                'message' => 'Invalid query params passed',
                'status_code' => 400,
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Internal server error',
                'status_code' => 500,
            ], 500);
        }
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
            'message' => 'Product updated successfully',
            'status_code' => 201,
            'data' => [
                'id' => $product->product_id,
                'name' => $product->name,
                'price' => $product->price,
                'description' => $product->description,
                'tag' => $product->tags,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ]

        ], 201);
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
