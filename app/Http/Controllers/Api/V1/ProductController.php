<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\CategoryProduct;
use App\Models\ProductVariant;
use App\Models\ProductVariantSize;
use App\Models\Size;
use App\Models\User;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProductRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
        // Handle the image upload
        // $imageUrl = null;
        // if ($request->hasFile('image')) {
        //     $imagePath = $request->file('image')->store('product_images', 'public');
        //     $imageUrl = Storage::url($imagePath); // Generate the URL for the uploaded image
        // }

        // Create the product
        $product = Product::create([
            'name' => $request->input('title'),
            'description' => $request->input('description'),
            'slug' => Carbon::now(),
            'tags' => $request->input('category'),
            'price' => $request->input('price'),
            // 'imageUrl' => $imageUrl,
            'imageUrl' => $request->input('image'),
            'user_id' => auth()->id(), // Assuming the user is authenticated
        ]);

        CategoryProduct::create([
            'category_id'=> $request->input('category'),
            'product_id'=> $product->product_id
        ]);

        $standardSize = Size::where('size', 'standard')->first('id');
        
        $productVariant = ProductVariant::create([
            'product_id' => $product->product_id,
            'stock' => $request->input('stock'),
            'stock_status' => $request->input('stock') > 0 ? 'in_stock' : 'out_stock',
            'price' => $request->input('price'),
            'size_id' => $standardSize->id,
        ]);

        ProductVariantSize::create([
            'product_variant_id' => $productVariant->id,
            'size_id' => $standardSize->id,
        ]);

        return response()->json(['message' => 'Product created successfully', 'product' => $product], 201);

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
