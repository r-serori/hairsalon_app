

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use Illuminate\Http\Request; // Add this line to import the Request class
use App\Http\Controllers\Auth\UserGetController; // Add this line to import the UserGetController class
use App\Http\Controllers\Auth\UserPostController; // Add this line to import the UserPostController class

Route::get('/sanctum/csrf-cookie', function (Request $request) {
    return response()->json([
        'message' => 'CSRF token has been set successfully.',
    ]);
});

Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware('guest');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest');

Route::prefix('/user')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/ownerRegister/{user_id}', [RegisteredUserController::class, 'ownerStore'])
            ->middleware('auth.session');

        //ユーザーが自分の個人情報を変更
        Route::post('/updateUser/{user_id}', [UserPostController::class, 'updateUser'])
            ->middleware('auth.session');

        Route::post('/forgot-password/{user_id}', [PasswordResetLinkController::class, 'store'])
            ->middleware('guest')
            ->name('password.email');

        Route::post('/reset-password/{user_id}', [NewPasswordController::class, 'store'])
            ->middleware('guest')
            ->name('password.store');

        Route::get('/verify-email/{user_id}/{hash}', VerifyEmailController::class)
            ->middleware(['auth', 'signed', 'throttle:6,1'])
            ->name('verification.verify');

        Route::post('/email/verification-notification/{user_id}', [EmailVerificationNotificationController::class, 'store'])
            ->middleware(['auth', 'throttle:6,1'])
            ->name('verification.send');

        Route::post('/logout}', [AuthenticatedSessionController::class, 'destroy'])
            ->middleware('auth.session');
    });
});



Route::prefix('user/{owner_id}')->group(function () {

    Route::middleware('auth:sanctum')->group(function () {

        Route::get('/getUsers/{user_id}', [UserGetController::class, 'getUsers'])
            ->middleware('auth.session');

        Route::get('/showUser/{user_id}', [UserGetController::class, 'show'])
            ->middleware('auth.session');

        Route::post('/updatePermission/{user_id}', [UserPostController::class, 'updatePermission'])
            ->middleware('auth.session');

        Route::post('/secondRegister', [RegisteredUserController::class, 'secondStore'])->middleware('auth.session');
    });
});
