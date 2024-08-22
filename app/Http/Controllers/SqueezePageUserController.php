<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SqueezePageUser;

class SqueezePageUserController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'title' => 'required|string|max:255',
        ]);

        $squeezePageUser = SqueezePageUser::create([
            'firstname' => $validatedData['firstname'],
            'lastname' => $validatedData['lastname'],
            'email' => $validatedData['email'],
            'title' => $validatedData['title'],
        ]);

        return response()->json([
            'message' => 'User details successfully added to squeeze_pages_user.',
            'data' => $squeezePageUser,
            'status_code' => 201,
        ], 201);
    }

    public function index(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Only admin users can get users.',
            ], Response::HTTP_FORBIDDEN);
        }

        $request->validate([
            'page' => 'integer|min:1',
            'limit' => 'integer|min:1',
        ]);

        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);

        $offset = ($page - 1) * $limit;
     
        $squeezePageUsers = SqueezePageUser::select('firstname', 'lastname', 'email', 'title', 'created_at')
                ->offset($offset)
                ->limit($limit)
                ->get();

        $totalItems = SqueezePageUser::count();
        $totalPages = ceil($totalItems / $limit);

        return response()->json([
            'message' => 'User details retrieved successfully.',
            'data' => $squeezePageUsers,
            'pagination' => [
                    'totalItems' => $totalItems,
                    'totalPages' => $totalPages,
                    'currentPage' => $page,
            ],
            'status_code' => 200,
        ], 200);
    }
}
