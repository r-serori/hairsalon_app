

<?php

use App\Actions\Fortify\ResetUserPassword;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use Illuminate\Http\Request; // Add this line to import the Request class
use App\Http\Controllers\Auth\UserGetController; // Add this line to import the UserGetController class
use App\Http\Controllers\Auth\UserPostController; // Add this line to import the UserPostController class
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Jetstream\DeleteUserMain;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Enums\Roles;
use App\Http\Controllers\Auth\UpdateUserInfoController;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Auth\getKeyController;

Route::middleware('api')->group(
    function () {


        Route::get('/search/{zipCode}', function ($code) {

            $decodedCode = urldecode($code);

            $response = Http::get('https://zipcloud.ibsnet.co.jp/api/search', [
                'zipcode' => $decodedCode,
            ]);

            return response()->json($response->json());
        });

        Route::middleware('auth:sanctum')->group(function () {

            Route::get('/vio-role', function () {
                try {
                    $user = User::find(Auth::id());
                    if ($user && $user->hasRole(Roles::$OWNER)) {

                        return response()->json([
                            'myRole' => 'オーナー'
                        ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                    } else if ($user && $user->hasRole(Roles::$MANAGER)) {

                        return response()->json([
                            'myRole' => 'マネージャー'
                        ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                    } else if ($user && $user->hasRole(Roles::$STAFF)) {

                        return response()->json([
                            'myRole' => 'スタッフ'
                        ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                    } else {
                        return response()->json([
                            'message' => '権限がありません。'
                        ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                    }
                } catch (\Exception $e) {
                    Log::error($e->getMessage());
                    return response()->json([
                        'message' => 'エラーが発生しました。もう一度やり直してください！',
                    ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }
            });


            Route::get('/check-session', function () {
                try {
                    $user = User::find(Auth::id());
                    if ($user && $user->hasRole(Roles::$OWNER) || $user->hasRole(Roles::$MANAGER) || $user->hasRole(Roles::$STAFF)) {

                        return response()->json(['status' => 'authenticated'], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                    } else {
                        return response()->json([
                            'status' => 'unauthenticated',
                        ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                    }
                } catch (\Exception $e) {
                    // Log::error($e->getMessage());
                    return response()->json([
                        'status' => 'unauthenticated',
                    ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }
            });

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
