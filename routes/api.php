

<?php

use App\Actions\Fortify\ResetUserPassword;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use Illuminate\Http\Request; // Add this line to import the Request class
use App\Http\Controllers\Auth\UserGetController; // Add this line to import the UserGetController class
use App\Http\Controllers\Auth\UserPostController; // Add this line to import the UserPostController class
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Jetstream\DeleteUserMain;

Route::middleware('api')->group(
    function () {
        Route::get('/sanctum/csrf-cookie', function (Request $request) {
            return response()->json([
                'message' => 'CSRF token has been set successfully.',
            ]);
        });

        //購入者ownerがuser登録するときの処理
        Route::post('/register', [RegisteredUserController::class, 'store'])
            ->middleware('guest');

        //ログイン処理
        Route::post('/login', [AuthenticatedSessionController::class, 'store'])
            ->middleware('guest');


        Route::middleware('auth:sanctum')->group(function () {

            Route::prefix('/user')->group(function () {
                //ログアウト処理 Gate,ALL
                Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);

                //各スタッフが自分の情報を取得 Gate,ALL
                Route::get('/showUser/{user_id}', [UserGetController::class, 'show']);

                //ユーザーが自分の個人情報を変更 Gate,ALL
                Route::post('/updateUser', [UpdateUserProfileInformation::class, 'update']);

                //ユーザーが自分のパスワードを変更 Gate,ALL
                Route::post('/updateUserPassword', [UpdateUserPassword::class, 'update']);

                //パスワードリセット　Gate,ALL
                Route::post('/resetPassword', [ResetUserPassword::class, 'reset'])
                    ->name('password.store');

                Route::get('/verify-email', VerifyEmailController::class)
                    ->middleware(['auth', 'signed', 'throttle:6,1'])
                    ->name('verification.verify');

                Route::post('/email/verification-notification/{user_id}', [EmailVerificationNotificationController::class, 'store'])
                    ->middleware(['auth', 'throttle:6,1'])
                    ->name('verification.send');
            });
        });


        Route::prefix('/user')->group(function () {

            //オーナーがスタッフの情報を取得 Gate,OWNER
            Route::get('/getUsers/{owner_id}', [UserGetController::class, 'getUsers']);

            Route::get('/getAttendanceUsers/{owner_id}', [UserGetController::class, 'getAttendanceUsers']);

            //オーナーがスタッフの権限を変更 Gate,OWNER
            Route::post('/updatePermission', [UserPostController::class, 'updatePermission']);

            //オーナーがスタッフを登録 Gate,OWNER
            Route::post('/staffRegister', [UserPostController::class, 'staffStore']);

            //オーナーがスタッフの情報を削除 Gate,OWNER
            Route::post('/deleteUser', [DeleteUserMain::class, 'deleteUser']);
        });
    }
);
