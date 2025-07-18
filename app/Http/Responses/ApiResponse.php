<?php

// app/Helpers/ApiResponse.php
namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function response_success($data = null, $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
        ], $status);
    }

    public static function response_error($errors = null, $message = null, $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message ?? 'Unexpected error occurred.',
            'errors' => $errors,
        ], $status);
    }
}
