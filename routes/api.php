

<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

Route::middleware('api')->group(
    function () {

        Route::get('/sanctum/csrf-cookie', function () {
            return response()->json([
                'message' => 'CSRF Cookie Set'
            ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        });

        Route::get('/search/{zipCode}', function ($code) {

            $decodedCode = urldecode($code);

            $response = Http::get('https://zipcloud.ibsnet.co.jp/api/search', [
                'zipcode' => $decodedCode,
            ]);

            return response()->json($response->json());
        });
    }
);
