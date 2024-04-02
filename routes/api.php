<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', function (Request $request) {
    $credentials = $request->only('login_id', 'password');

    if (Auth::attempt($credentials)) {
        $user = User::where('login_id', $request->login_id)->first();
        $token = $user->createToken('token-name')->plainTextToken;

        return response()->json(['token' => $token]);
    }

    return response()->json(['error' => 'Unauthorized'], 401);
});




Route::post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();

    return response()->json(['message' => 'Logged out'], 200);
});

Route::post('/register', function (Request $request) {
    $user = new User();
    $user->login_id = $request->login_id;
    $user->password = bcrypt($request->password);
    $user->save();

    return response()->json(['message' => 'User created'], 201);
});
