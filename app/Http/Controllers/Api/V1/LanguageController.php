<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use App\Models\Language;

class LanguageController extends Controller
{
    public function create(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'language' => 'required|string|unique:languages',
            'code' => 'required|string|unique:languages',
            'description' => 'string|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 400);
        }

        $language = Language::create([
            'id' => Str::uuid(),
            'language' => $request->language,
            'code' => $request->code,
            'description' => $request->description,
        ]);

        return response()->json([
            'status' => 201,
            'message' => 'Language Created Successfully',
            'language' => $language
        ], 201);
    }

    public function index()
    {
        // if (!auth()->check()) {
        //     return response()->json([
        //         'status' => 401,
        //         'message' => 'Unauthorized'
        //     ], 401);
        // }

       

        $languages = Language::select('language', 'code')->get();

        return response()->json([
            'status' => 200,
            'message' => 'Languages fetched successfully',
            'languages' => $languages
        ], 200);
    }
}

