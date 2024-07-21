<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(CustomerRequest $request)
    {
        try {
            $limit = $request->input('limit');
            $page = $request->input('page');

            $customers = User::with('organisations')
                ->where('role', 'customer')
                ->paginate($limit, ['*'], 'page', $page);

            return response()->json([
                'status_code' => 200,
                'current_page' => $customers->currentPage(),
                'total_pages' => $customers->lastPage(),
                'limit' => $customers->perPage(),
                'total_items' => $customers->total(),
                'data' => $customers->map(function ($customer) {
                    return [
                        'name' => $customer->name, 
                        'email' => $customer->email,
                        'phone' => $customer->phone,
                        'organisations' => $customer->organisations->pluck('org_id')
                    ];
                })
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching customers: ' . $e->getMessage());
            dd($e);
            return response()->json([
                'error' => 'Bad Request',
                "message" => "Internal server error",
                "status_code" => 500
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
