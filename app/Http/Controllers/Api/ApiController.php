<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

abstract class ApiController extends Controller
{
    protected function ok(mixed $data, array $meta = [], int $status = 200): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'meta' => array_merge(['request_id' => request()->attributes->get('request_id')], $meta),
        ], $status);
    }

    protected function error(string $code, string $message, array $details = [], int $status = 400): JsonResponse
    {
        return response()->json([
            'error' => [
                'code' => $code,
                'message' => $message,
                'details' => $details,
                'request_id' => request()->attributes->get('request_id'),
            ],
        ], $status);
    }
}
