<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Js;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Models\User;


class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): JsonResponse
    {
        try {
            $request->validate([

                'email' => ['required', 'string', 'email'],
                // 'unique:users'
                'password' => [
                    'required',
                ],

            ]);

            // ユーザーが存在するかどうかを確認
            $userExists = Auth::attempt($request->only('email, password, '));

            if (!$userExists) {
                // ユーザーが存在しない場合はエラーレスポンスを返す
                return response()->json([
                    'resStatus' => 'error',
                    'message' => 'ログインIDまたはパスワードが間違っています。'
                ], 400);
            } else {

                $request->authenticate();

                $request->session()->regenerate();

                $user = Auth::user();

                $responseUser = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ];

                return  response()->json([
                    'resStatus' => "success",
                    'message' => 'ログインに成功しました。',
                    'responseUser' => $responseUser,
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => 'error',
                'message' => 'ログインに失敗しました。',
            ], 400);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): JsonResponse
    {
        try {
            Auth::guard('web')->logout();

            $request->session()->invalidate();

            $request->session()->regenerateToken();

            return response()->json([
                'resStatus' => "success",
                'message' => 'ログアウトに成功しました。',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => 'error',
                'message' => 'ログアウトに失敗しました。',
            ], 400);
        }
    }
}
