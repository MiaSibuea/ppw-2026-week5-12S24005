<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function apiSuccess(
        mixed $data,
        string $message = 'Permintaan berhasil.',
        int $status = 200,
        array $meta = [],
    ): JsonResponse {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'meta' => (object) $meta,
        ], $status);
    }
}
