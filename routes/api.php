

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

        Route::post('/register', [RegisteredUserController::class, 'store'])
            ->middleware('guest');

        Route::post('/login', [AuthenticatedSessionController::class, 'store'])
            ->middleware('guest');

        Route::middleware('auth:sanctum')->group(function () {

            Route::prefix('/user')->group(function () {

                // Route::post('/ownerRegister', [UserPostController::class, 'ownerStore']);

                Route::post('/ownerRegister', [UserPostController::class, 'ownerStore']);

                Route::get('/showUser/{user_id}', [UserGetController::class, 'show']);

                //ユーザーが自分の個人情報を変更
                Route::post('/updateUser/{user_id}', [UpdateUserProfileInformation::class, 'update']);
                //ユーザーが自分のパスワードを変更
                Route::post('/updateUserPassword/{user_id}', [UpdateUserPassword::class, 'update']);

                Route::post('/forgotPassword/{user_id}', [PasswordResetLinkController::class, 'store'])
                    ->name('password.email');

                Route::post('/resetPassword/{user_id}', [ResetUserPassword::class, 'reset'])
                    ->name('password.store');

                Route::get('/verify-email/{user_id}/{hash}', VerifyEmailController::class)
                    ->middleware(['auth', 'signed', 'throttle:6,1'])
                    ->name('verification.verify');

                Route::post('/email/verification-notification/{user_id}', [EmailVerificationNotificationController::class, 'store'])
                    ->middleware(['auth', 'throttle:6,1'])
                    ->name('verification.send');

                Route::post('/logout}', [AuthenticatedSessionController::class, 'destroy']);
            });
        });


        Route::prefix('/user/{owner_id}')->group(function () {

            Route::get('/getUsers/{user_id}', [UserGetController::class, 'getUsers']);

            Route::post('/updatePermission/{user_id}', [UserPostController::class, 'updatePermission']);

            Route::post('/staffRegister', [UserPostController::class, 'staffStore']);

            Route::post('/deleteUser/{user_id}', [DeleteUserMain::class, 'delete']);
        });
    }
);
