<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Squeeze;
use Illuminate\Support\Facades\Mail;
use App\Mail\SqueezeTemplateMail;
use Illuminate\Database\QueryException;
use App\Jobs\SendSqueezeEmail;

class SqueezeController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email|unique:squeezes,email',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'location' => 'required|string|max:255',
            'job_title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'interests' => 'required|array',
            'referral_source' => 'required|string|max:255',
        ]);

        try {
            // Store user data in the database
            $squeeze = Squeeze::create($validatedData);

            // Dispatch the job to the queue
            SendSqueezeEmail::dispatch($squeeze);

            return response()->json(['message' => 'Your request has been received. You will get a template shortly.'], 200);
        } catch (QueryException $e) {
            if ($e->getCode() == '23505') {

                return response()->json(['message' => 'Email address already exists.', 'status_code' => 409], 409);
            }
            return response()->json(['message' => 'Failed to submit your request', 'status_code' => 400, 'error' => $e->getMessage()], 400);
        }
    }
}
