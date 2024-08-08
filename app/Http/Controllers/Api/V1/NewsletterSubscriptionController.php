<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NewsletterSubscription;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Http\Response;
use Exception;

class NewsletterSubscriptionController extends Controller
{
    protected $validator;

    public function __construct(ValidationFactory $validator)
    {
        $this->validator = $validator;
    }

    public function store(Request $request)
    {
        $validator = $this->validator->make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $newsletter_subscription = NewsletterSubscription::create([
                'email' => $request->email
            ]);

            return response()->json([
                'data' => $newsletter_subscription,
                'message' => 'Newsletter Subscribed Successfully',
                'status_code' => 201
            ], Response::HTTP_CREATED);
        } catch (Exception $exception) {
            return response()->json([
                'message' => 'Internal Server Error',
                'status_code' => 500
            ], 500);
        }
    }
}

