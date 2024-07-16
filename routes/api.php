

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
use Illuminate\Support\Facades\Http;

Route::middleware('api')->group(
    function () {
        Route::get('/sanctum/csrf-cookie', function (Request $request) {
            Log::info('CSRF token has been set successfully.');
            return response()->json([
                'message' => 'CSRF token has been set successfully.',
            ]);
        });


        Route::get('/search/{zipCode}', function ($code) {

            $decodedCode = urldecode($code);

            $response = Http::get('https://zipcloud.ibsnet.co.jp/api/search', [
                'zipcode' => $decodedCode,
            ]);

            return response()->json($response->json());
        });
        Route::middleware('guest')->group(
            function () {
                //購入者ownerがuser登録するときの処理
                Route::post('/register', [RegisteredUserController::class, 'store']);


                //ログイン処理
                Route::post('/login', [AuthenticatedSessionController::class, 'store']);



                Route::post('/forgotPassword', [PasswordResetLinkController::class, 'store'])
                    ->name('password.email');


                //パスワードリセット　Gate,ALL
                Route::post('/resetPassword', [ResetUserPassword::class, 'resetPassword'])
                    ->name('password.reset');
            }
        );



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

            Route::get('/getKey', function () {
                try {
                    $user = User::find(Auth::id());
                    if ($user && $user->hasRole(Roles::$OWNER) || $user->hasRole(Roles::$MANAGER) || $user->hasRole(Roles::$STAFF)) {


                        return response()->json(['roleKey' => env('REACT_APP_ENCRYPTION_KEY')], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                    } else {
                        return response()->json([
                            'message' => '権限がありません。',
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


            Route::post('/email/verification-notification/{user_id}', [EmailVerificationNotificationController::class, 'store'])
                ->middleware(['auth', 'throttle:6,1'])
                ->name('verification.send');

            Route::prefix('/user')->group(function () {

                Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
                    ->middleware(['auth', 'signed'])->name('verification.verify');

                //ログアウト処理 Gate,ALL
                Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);

                //各スタッフが自分の情報を取得 Gate,ALL
                Route::get('/showUser', [UserGetController::class, 'show']);

                //ユーザーが自分の個人情報を変更 Gate,ALL
                Route::post('/updateUser', [UpdateUserProfileInformation::class, 'updateUser']);

                //ユーザーが自分のパスワードを変更 Gate,ALL
                Route::post('/updateUserPassword', [UpdateUserPassword::class, 'updateFromRequest']);
            });
        });

        Route::prefix('/user')->group(function () {
            //オーナーがスタッフの情報を取得 Gate,OWNER
            Route::get('/getUsers', [UserGetController::class, 'getUsers']);

            Route::get('/getAttendanceUsers', [UserGetController::class, 'getAttendanceUsers']);

            //オーナーがスタッフの権限を変更 Gate,OWNER
            Route::post('/updatePermission', [UserPostController::class, 'updatePermission']);

            //オーナーがスタッフを登録 Gate,OWNER
            Route::post('/staffRegister', [UserPostController::class, 'staffStore']);

            //オーナーがスタッフの情報を削除 Gate,OWNER
            Route::post('/deleteUser', [DeleteUserMain::class, 'deleteUser']);
        });
    }
);
