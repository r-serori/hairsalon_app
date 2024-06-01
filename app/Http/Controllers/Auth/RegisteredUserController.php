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
                'login_id' => ['required', 'string', 'max:50'],
                // 'unique:users'
                'password' => [
                    'required',
                ],
            ]);

            $userID = User::where('login_id', $request->login_id)->first();


            if ($userID) {
                return
                    response()->json([
                        "resStatus" => 'error',
                        'message' => 'ログインIDが既に存在しています。',
                    ], 400);
            } else {
                $user = User::create([
                    'login_id' => $request->login_id,
                    'password' => Hash::make($request->password),
                ]);

                event(new Registered($user));

                Auth::login($user);

                $responseUser = [
                    'id' => $user->id,
                    'login_id' => $user->login_id,
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
