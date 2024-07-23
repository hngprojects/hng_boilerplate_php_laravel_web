<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait HttpResponses
{

    protected function apiResponse($status = 'success', $message = '', $status_code = 200, $data = null): JsonResponse
    {
        $response = [
            'status' => $status,
            'message' => $message,
            'status_code' => $status_code,
        ];

        // Conditionally add the 'data' key if $data is not null
        if ($data !== null) {
            $response['data'] = $data;
        }

        // Return the JSON response
        return response()->json($response, $status_code);
    }
}
