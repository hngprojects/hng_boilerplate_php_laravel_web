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

    public static function response($status, $message, $data = null, $statusCode = 200 ){
        $response = [
            'status' => $status,
            'message' => $message,
        ];
        if (!is_null($data)) {
            $response['data'] = $data;
        }
        if (!($statusCode == 200 || $statusCode == 201)) {
            $response['statusCode'] = $statusCode;
        }
        return response()->json($response, $statusCode);
    }
}
