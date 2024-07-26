<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
use App\Models\User; // 追加：Userモデルを使用するためにインポート
use App\Notifications\ResetPasswordNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;


class PasswordResetLinkController extends BaseController
{
    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required | email | max:200| exists:users,email',
            ]);

            if ($validator->fails()) {
                return  $this->responseMan(
                    ['message' => '入力内容をご確認ください。'],
                    400
                );
            }

            $validateData = (object)$validator->validate();

            // ユーザーを取得して存在するか確認する
            $user = User::where('email', $validateData->email)->first();

            if (!$user) {
                return $this->responseMan(
                    ['message' => 'ユーザーが見つかりません。'],
                    404
                );
            }

            // パスワードリセットトークンを生成
            $token = Password::createToken($user);

            // パスワードリセットテーブルにトークンを保存
            DB::table('password_resets')->updateOrInsert(
                ['email' => $validateData->email],
                [
                    'email' => $validateData->email,
                    'token' => $token,
                    'created_at' => Carbon::now()
                ]
            );

            // パスワードリセット通知を送信する
            $user->notify(new ResetPasswordNotification($token));

            DB::commit();

            return $this->responseMan(
                ['message' => 'パスワードリセットメールを送信しました！']

            );
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return $this->responseMan(
                ['message' => 'エラーが発生しました。もう一度やり直してください！'],
                500
            );
        }
    }
}
