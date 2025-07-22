<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * @param mixed $data
     * @param int $status
     */
    public static function success(mixed $data = null, int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
        ], $status);
    }

    /**
     * @param mixed|null $errors
     * @param string|null $message
     * @param int $status
     */
    public static function error(mixed $errors = null, ?string $message = null, int $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message ?? 'Unexpected error occurred.',
            'errors' => $errors,
        ], $status);
    }
}
