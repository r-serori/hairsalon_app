<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;


class RegisteredUserController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        try {


            Log::info('ユーザー登録処理を開始します。');
            Log::info('リクエストデータ: ' . $request);

            $request->validate([
                'name' => ['required', 'string', 'max:50'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                // 'unique:users'
                'password' => [
                    'required',
                ],
                'role' => ['required', 'string', 'max:10'],
            ]);

            $userID = User::where('email', $request->email)->first();


            if ($userID) {
                return
                    response()->json([
                        "resStatus" => 'error',
                        'message' => 'ログインIDが既に存在しています。',
                    ], 400);
            } else {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role' => $request->role,
                ]);

                event(new Registered($user));

                Auth::login($user);

                $responseUser = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ];
                return response()->json(
                    [
                        'resStatus' => "success",
                        'message' => 'ユーザー登録に成功しました。',
                        'responseUser' => $responseUser,
                    ],
                    200
                );
            }
        } catch (\Exception $e) {
            Log::error('ユーザー登録処理中にエラーが発生しました。');
            Log::error('エラー内容: ' . $e);
            return response()->json([
                "resStatus" => 'error',
                'message' => 'ユーザー登録に失敗しました。',
            ], 400);
        }
    }
}
