<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProductRequest;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


    // Implementation for the product listing categories API endpoint
    public function categories(Request $request)

    {

        try {
            // Validate query parameters
            $this->validate($request, [
                'limit' => 'nullable|integer|min:1',
                'offset' => 'nullable|integer|min:0',
                'parent_id' => 'nullable|integer',
            ]);
    
            // Get query parameters
            $limit = $request->input('limit', 10);
            $offset = $request->input('offset', 0);
            $parentId = $request->input('parent_id');
    
            // Implement efficient database querying
            $categories = Category::when($parentId, function ($query) use ($parentId) {
                $query->where('parent_id', $parentId);
            })
                ->offset($offset)
                ->limit($limit)
                ->get();
    
            // Implement caching mechanism
            $cacheKey = 'categories_' . $offset . '_' . $limit . '_' . $parentId;
            $categories = Cache::remember($cacheKey, 60, function () use ($categories) {
                return $categories;
            });
    
            // Return response
            return response()->json([
                'status_code' => 200,
                'categories' => $categories,
            ]);
        } catch (\Exception $e) {
            // Log the error
            Log::error($e->getMessage());
    
            // Return a 500 error response
            return response()->json([
                'status_code' => 500,
                'error' => 'Internal Server Error',
            ], 500);
        }        

    }
}
