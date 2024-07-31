<?php

namespace App\Helpers;

class ResponseHelper
{
    public static function validationError($errors)
    {
        $formattedErrors = [];
        foreach ($errors->toArray() as $field => $messageArray) {
            foreach ($messageArray as $message) {
                $formattedErrors[] = [
                    'field' => $field,
                    'message' => $message,
                ];
            }
        }
        return response()->json([
            'errors' => $formattedErrors,
        ], 422);
    }
    public static function response($message, $statusCode, $data = [])
    {
      $successCodes = [200, 201, 202, 204];
        return response()->json([
            'status' => in_array($statusCode, $successCodes) ? 'success' : 'error',
            'message' => $message,
            'status_code' => $statusCode,
            'data' => $data
        ], $statusCode);
    }


//    public static function response($message, $status_code, $data = null ){
//        $response = [
//            'message' => $message,
//            'status_code' => $status_code,
//            'data' => $data
//        ];
//        return response()->json($response, $status_code);
//    }
}
