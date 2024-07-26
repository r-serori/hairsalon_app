

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserGetController; // Add this line to import the UserGetController class
use App\Http\Controllers\Auth\UserPostController; // Add this line to import the UserPostController class
use App\Actions\Jetstream\DeleteUserMain;
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

        Route::middleware('auth:sanctum')->group(function () {

            Route::prefix('/user')->group(function () {

                //購入者ownerが店の情報を登録
                Route::post('/ownerRegister', [UserPostController::class, 'ownerStore']);

                //購入者ownerが店の情報を更新
                Route::post('/updateOwner', [UserPostController::class, 'ownerUpdate']);

                //各スタッフが自分の情報を取得 Gate,ALL
                Route::get('/getOwner', [UserGetController::class, 'getOwner']);

                //各スタッフが自分の情報を取得 Gate,ALL
                Route::get('/showUser', [UserGetController::class, 'show']);

                //オーナーがスタッフの情報を取得 Gate,OWNER
                Route::get('/getUsers', [UserGetController::class, 'getUsers']);

                //オーナーがスタッフの権限を変更 Gate,OWNER
                Route::post('/updatePermission', [UserPostController::class, 'updatePermission']);

                //オーナーがスタッフを登録 Gate,OWNER
                Route::post('/staffRegister', [UserPostController::class, 'staffStore']);

                //オーナーがスタッフの情報を削除 Gate,OWNER
                Route::post('/deleteUser', [DeleteUserMain::class, 'deleteUser']);
            });
        });
    }
);
