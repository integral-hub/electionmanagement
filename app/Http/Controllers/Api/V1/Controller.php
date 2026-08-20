<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\JsonResponse;

abstract class Controller extends BaseController
{
    /**
     * Consistent success envelope for all v1 API responses.
     */
    protected function success(mixed $data = null, string $message = 'OK', int $status = 200): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    /**
     * Consistent error envelope for all v1 API responses.
     */
    protected function fail(string $message = 'Something went wrong.', int $status = 400, mixed $errors = null): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }
}
