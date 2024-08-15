<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait HttpResponses
{

    protected function apiResponse($message = '', $status_code = 200, $data = null, $token = null): JsonResponse
    {
        $response = [
            'status_code' => $status_code,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['access_token'] = $token;
        }

        // Conditionally add the 'data' key if $data is not null
        if ($data !== null) {
            $response['data'] = $data;
        }

        // Return the JSON response
        return response()->json($response, $status_code);
    }

    public function validationErrorResponseAlign($errors) : JsonResponse {
        $response = [
            'status' => Response::HTTP_BAD_REQUEST,
            'title' => 'One or more validation errors occurred.',
            'errors' => $errors,
        ];
        return response()->json($response, Response::HTTP_BAD_REQUEST);
    }

    public function validationErrorResponse($errors) : JsonResponse {
        $response = [
            'status_code' => Response::HTTP_BAD_REQUEST,
            'message' => 'One or more validation errors occurred.',
            'errors' => $errors,
        ];
        return response()->json($response, Response::HTTP_BAD_REQUEST);
    }
}
