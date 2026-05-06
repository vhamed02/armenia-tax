<?php

namespace App\Http\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success(mixed $data = null, string $message = 'OK', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $data,
            'message' => $message,
        ], $status);
    }

    public static function error(string $message, int $status = 400, mixed $data = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'data'    => $data,
            'message' => $message,
        ], $status);
    }

    public static function amd(int $raw): array
    {
        return [
            'raw'       => $raw,
            'formatted' => number_format($raw) . ' AMD',
        ];
    }
}
