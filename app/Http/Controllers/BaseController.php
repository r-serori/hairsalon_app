<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class BaseController extends Controller
{
    protected function responseMan(array $data = [], int $status = 200): JsonResponse
    {
        return response()->json($data, $status, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
    }
}
