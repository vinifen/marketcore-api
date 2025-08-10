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
    public static function error(
        ?string $message = null,
        mixed $errors = null,
        int $status = 400,
    ): JsonResponse {
        $errorArray = [];

        if (is_array($errors)) {
            $errorArray = $errors;
        } elseif (!is_null($errors)) {
            $errorArray = ['detail' => $errors];
        }

        return response()->json([
            'success' => false,
            'errors' => array_merge(
                ['message' => $message ?? 'Unexpected error occurred.'],
                $errorArray
            ),
        ], $status);
    }
}
