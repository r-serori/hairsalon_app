<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use \Illuminate\Database\Eloquent\Collection;

abstract class BaseController extends Controller
{
    protected function responseMan(array|Collection $data = [], int $status = 200): JsonResponse
    {
        return response()->json($data, $status, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
    }
    protected function serverErrorResponseWoman(): JsonResponse
    {
        return response()->json(['message' => '内部サーバーエラーが発生しました'], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
    }
}
