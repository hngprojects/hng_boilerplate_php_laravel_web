<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\CategoryProduct;
use App\Models\OrganisationUser;
use App\Models\ProductVariant;
use App\Models\ProductVariantSize;
use App\Models\Size;
use App\Http\Requests\UpdateProductRequest;
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
use App\Http\Resources\ProductResource;


class ProductController extends Controller
{
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'minPrice' => 'nullable|numeric|min:0',
            'maxPrice' => 'nullable|numeric|min:0',
            'status' => 'nullable|string|in:in_stock,out_of_stock,low_on_stock',
            'page' => 'nullable|integer|min:1',
            'limit' => 'nullable|integer|min:1|max:100',
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
                'status_code' => 422
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

        if ($request->filled('status')) {
            $query->whereHas('productsVariant', function ($q) use ($request) {
                $q->where('stock_status', $request->status);
            });
        }


        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $products = $query->with(['productsVariant', 'categories'])
            ->paginate($limit, ['*'], 'page', $page);

        $transformedProducts = $products->map(function ($product) {
            return [
                'name' => $product->name,
                'price' => $product->price,
                'imageUrl' => $product->imageUrl,
                'description' => $product->description,
                'product_id' => $product->product_id,
                'quantity' => $product->quantity,
                'category' => $product->categories->isNotEmpty() ? $product->categories->map->name : [],
                'stock' => $product->productsVariant->isNotEmpty() ? $product->productsVariant->first()->stock : null,
                'status' => $product->productsVariant->isNotEmpty() ? $product->productsVariant->first()->stock_status : null,
                'date_added' => $product->created_at
            ];
        });

        return response()->json([
            'success' => true,
            'products' => $transformedProducts,
            'pagination' => [
                'totalItems' => $products->total(),
                'totalPages' => $products->lastPage(),
                'currentPage' => $products->currentPage(),
                'perPage' => $products->perPage(),
            ],
            'status_code' => 200
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

            $products = Product::select(
                'product_id',
                'name',
                'price',
                'imageUrl',
                'description',
                'created_at',
                'quantity'
            )
                ->with(['productsVariant', 'categories'])
                ->offset($offset)
                ->limit($limit)
                ->get();

            // Get total product count
            $totalItems = Product::count();
            $totalPages = ceil($totalItems / $limit);

            $transformedProducts = $products->map(function ($product) {
                return [
                    'name' => $product->name,
                    'price' => $product->price,
                    'imageUrl' => $product->imageUrl,
                    'description' => $product->description,
                    'product_id' => $product->product_id,
                    'quantity' => $product->quantity,
                    'category' => $product->categories->isNotEmpty() ? $product->categories->map->name : [],
                    'stock' => $product->productsVariant->isNotEmpty() ? $product->productsVariant->first()->stock : null,
                    'status' => $product->productsVariant->isNotEmpty() ? $product->productsVariant->first()->stock_status : null,
                    'date_added' => $product->created_at
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Products retrieved successfully',
                'products' => $transformedProducts,
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
                'error' => $e->getMessage(),
                'status_code' => 500,
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateProductRequest $request, $org_id)
    {
        $isOwner = OrganisationUser::where('org_id', $org_id)->where('user_id', auth()->id())->exists();

        if (!$isOwner) {
            return response()->json(['message' => 'You are not authorized to create products for this organisation.'], 403);
        }

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('product_images', 'public');
            $imageUrl = Storage::url($imagePath);
        }

        $product = Product::create([
            'name' => $request->input('title'),
            'description' => $request->input('description'),
            'slug' => Carbon::now(),
            'tags' => $request->input('category'),
            'price' => $request->input('price'),
            'imageUrl' => $imageUrl,
            'user_id' => auth()->id(),
            'org_id' => $org_id
        ]);

        CategoryProduct::create([
            'category_id' => $request->input('category'),
            'product_id' => $product->product_id
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
    public function show($product_id)
    {
        $product = Product::find($product_id);
        // return $product_id;
        if (!$product) {
            return response()->json([
                'status' => 'error',
                "message" => "Product not found",
                'status_code' => 404,
            ]);
        }
        $product = new ProductResource($product);
        return response()->json([
            'status' => 'success',
            "message" => "Product retrieve ",
            'status_code' => 200,
            'data' => $product
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, string $org_id, string $product_id)
    {
        $org_id = $request->route('org_id');

        $isOwner = OrganisationUser::where('org_id', $org_id)->where('user_id', auth()->id())->exists();

        if (!$isOwner) {
            return response()->json(['message' => 'You are not authorized to update products for this organisation.'], 403);
        }

        $validated = $request->validated();

        $product = Product::findOrFail($product_id);
        $product->update([
            'name' => $validated['name'] ?? $product->name,
            'is_archived' => $validated['is_archived'] ?? $product->is_archived,
            'imageUrl' => $validated['image'] ?? $product->imageUrl
        ]);

        foreach ($request->input('productsVariant') as $variant) {
            $existingProductVariant = ProductVariant::where('product_id', $product->product_id)
                ->where('size_id', $variant['size_id'])
                ->first();

            if ($existingProductVariant) {
                $existingProductVariant->update([
                    'stock' => $variant['stock'],
                    'stock_status' => $variant['stock'] > 0 ? 'in_stock' : 'out_stock',
                    'price' => $variant['price'],
                ]);
            } else {
                $newProductVariant = ProductVariant::create([
                    'product_id' => $product->product_id,
                    'stock' => $variant['stock'],
                    'stock_status' => $variant['stock'] > 0 ? 'in_stock' : 'out_stock',
                    'price' => $variant['price'],
                    'size_id' => $variant['size_id'],
                ]);

                ProductVariantSize::create([
                    'product_variant_id' => $newProductVariant->id,
                    'size_id' => $variant['size_id'],
                ]);
            }
        }

        return response()->json(['message' => 'Product updated successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($org_id, $product_id)
    {

        $isOwner = OrganisationUser::where('org_id', $org_id)->where('user_id', auth()->id())->exists();
        // Check if the user's organisation matches the org_id in the request
        if (!$isOwner) {
            return response()->json(
                [
                    'status' => 'Forbidden',
                    'message' => 'You do not have permission to delete a product from this organisation.',
                    'status_code' => 403
                ],
                403
            );
        }

        $product = Product::find($product_id);

        if (!$product) {
            return response()->json([
                'error' => 'Product not found',
                'message' => "The product with ID $product_id does not exist."
            ], 404);
        }

        // Check if the product belongs to the organisation
        if ($product->org_id !== $org_id) {
            return response()->json([
                'error' => 'Forbidden',
                'message' => 'You do not have permission to delete this product.'
            ], 403);
        }

        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully.'
        ], 204);
    }
}
