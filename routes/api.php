<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Http;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::get('/csrf-token', function () {
//     return response()->json(['csrfToken' => csrf_token()]);
// });


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::post('/login', function (Request $request) {
//     $credentials = $request->only('login_id', 'password');

//     if (Auth::attempt($credentials)) {
//         $user = User::where('login_id', $request->login_id)->first();
//         $token = $user->createToken('token-name')->plainTextToken;

//         return response()->json([
//             'token' => $token,
//             "user" => $user,
//             "message" => "ログインしました。"
//         ]);
//     }

//     return response()->json(['error' => 'Unauthorized'], 401);
// });




// Route::post('/logout', function (Request $request) {
//     $request->user()->currentAccessToken()->delete();

//     return response()->json(['message' => 'Logged out'], 200);
// });

// Route::post('/register', function (Request $request) {
//     $user = new User();
//     $user->login_id = $request->login_id;
//     $user->password = bcrypt($request->password);
//     $user->save();

//     return response()->json(['message' => 'User created'], 201);
// });

// Route::post('/login', function (Request $request) {
//     // ここで必要なデータを処理し、auth.phpの/loginにリクエストを送る
//     $response = Http::get(url('/login'));

//     // 必要に応じてレスポンスを返す
//     return $response->body();
// });

// Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('guest')->name('login');

// Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('guest')->name('register');

// Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth:sanctum')->name('logout');
