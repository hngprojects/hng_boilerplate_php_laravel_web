<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait ApiResponse {
    /**
     * Generate a standardized API response
     *
     * @param string $status Status of the response (success, error, etc.)
     * @param int $statusCode HTTP status code
     * @param string $message Human-readable message
     * @param array $data Additional data to include in response
     * @return array The formatted response array
     */
    protected function apiResponse(string $status, int $statusCode, string $message, array $data = []): array
    {
        return [
            'status' => $status,
            'status_code' => $statusCode,
            'message' => $message,
            'data' => $data
        ];
    }
    
    /**
     * Generate a success response
     *
     * @param string $message Success message
     * @param array $data Additional data
     * @param int $statusCode HTTP status code, defaults to 200
     * @return array
     */
    protected function successResponse(string $message, array $data = [], int $statusCode = 200): array
    {
        return $this->apiResponse('success', $statusCode, $message, $data);
    }
    
    /**
     * Generate an error response
     *
     * @param string $message Error message
     * @param int $statusCode HTTP status code, defaults to 400
     * @param array $data Additional data
     * @return array
     */
    protected function errorResponse(string $message, int $statusCode = 400, array $data = []): array
    {
        return $this->apiResponse('error', $statusCode, $message, $data);
    }
}