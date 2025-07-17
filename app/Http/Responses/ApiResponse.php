<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function success($data = null, $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
        ], $status);
    }

    protected function error($errors = null, $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'errors' => $errors,
        ], $status);
    }
}
