<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function response_success($data = null, $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
        ], $status);
    }

    protected function response_error($errors = null, $status = 400, $message = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message ?? 'Unexpected error occurred.',
            'errors' => $errors,
        ], $status);
    }
}
