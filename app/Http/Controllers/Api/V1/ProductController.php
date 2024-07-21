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
    public function store(CreateProductRequest $request)
    {
        $request->validated();


        //uncomment this code when the auth system is completed on this project
        // $user = auth()->user();
        // $product = $user->products()->create($request->all());


        //create product based on seeded data 
        $user = User::first(); 

        if($user){

            $product = Product::create([
                'user_id' => $user->id, 
                'name' => $request->name,
                'description' => $request->description
            ]);
    
            return response()->json([
                'status_code' => 201,
                'product_id' => $product->product_id,
                'name' => $product->name,
                'description' => $product->description,
                'message' => 'Product created successfully'
            ], 201);
        }else{

            return response()->json([
                'message' => 'Request successfull, kindly seed the db to create a product'
            ], 200);
        }

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
}
