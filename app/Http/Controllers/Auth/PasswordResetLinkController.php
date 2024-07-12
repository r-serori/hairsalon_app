<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
use App\Models\User; // 追加：Userモデルを使用するためにインポート
use App\Notifications\ResetPasswordNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class PasswordResetLinkController extends Controller
{
    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // ユーザーを取得して存在するか確認する
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['指定されたメールアドレスのユーザーは見つかりませんでした。'],
            ]);
        }

        // パスワードリセットトークンを生成
        $token = Password::createToken($user);

        // パスワードリセットテーブルにトークンを保存
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );

        // パスワードリセット通知を送信する
        $user->notify(new ResetPasswordNotification($token));

        return response()->json(['status' => 'パスワードリセットのメールを送信しました。']);
    }
}
