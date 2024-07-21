<?php

namespace App\Traits;

trait HttpResponses
{
    protected function success(array|object $data, bool $status, string $message = null, int $code = 200)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'status_code' => $code,
            'data' => $data,
        ], $code);
    }



    protected function error(bool $status, string $message = null, int $code = 400, array|string $data = null)
    {
        if (!$data) {
            return response()->json([
                'status' => $status,
                'message' => $message,
                'status_code' => $code,
            ], $code);
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'status_code' => $code,
            'data' => $data,
        ], $code);
    }
}
