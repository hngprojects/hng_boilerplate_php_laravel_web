<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\WaitList;

class WaitListController extends Controller
{

    public function index()
    {

        $waitlists = WaitList::all();

        return response()->json([
            'status' => 200,
            'data' => $waitlists
        ], 200);
    }


    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:wait_lists,email'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $errorMessage = implode(" ", $errors);

            return response()->json([
                'status' => 422,
                'message' => $errorMessage
            ], 422);
        }

        try {

            $waitlist = WaitList::create([
                'name' => $request->name,
                'email' => $request->email
            ]);

            return response()->json([
                'status' => 201,
                'message' => 'You have been added to the waitlist and an email has been sent.',
                'data' => $waitlist
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => 'Failed to submit your request',
            ], 400);
        }
    }


}
