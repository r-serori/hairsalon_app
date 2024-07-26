<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Enums\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class UpdateUserPassword implements UpdatesUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and update the user's password.
     *
     * @param  array<string, string>  $input
     * @return void
     */
    public function update($user, array $input): void
    {
    }

    /**
     * Custom method to handle Request.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function updateFromRequest(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER) || $user->hasRole(Roles::$MANAGER) || $user->hasRole(Roles::$STAFF)) {

                // パスワードのバリデーション
                Validator::make($request->all(), [
                    'current_password' => ['required', 'string', 'current_password:web'],
                    'password' => $this->passwordRules(),
                    'password' => $this->passwordRules(),
                    'password_confirmation' => ['required', 'string'],
                ], [
                    'current_password.current_password' => __('送信されたパスワードが既存のパスワードと一致しません！もう一度試してください！'),
                    'password_confirmation.same' => __('パスワードと確認フィールドが一致していません！'), // エラーメッセージ追加
                ])->validateWithBag('updatePassword');

                // パスワードの更新
                $user->forceFill([
                    'password' => Hash::make($request->input('password')),
                ])->save();

                // ユーザーをログアウトさせる
                Auth::guard('web')->logout();
                if ($request->hasSession()) {
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                };

                DB::commit();

                return response()->json(['message' => 'パスワードが正常に更新されました！ログインし直してください！']);
            } else {
                DB::rollBack();
                return response()->json([
                    'message' => 'あなたは権限がありません。',
                ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            if ($e->validator->errors()->has('current_password')) {
                // パスワードが間違っている場合のエラーメッセージ
                return response()->json(['message' => __('送信されたパスワードが既存のパスワードと一致しません！もう一度試してください！')], 422);
            } else {

                return response()->json([
                    'message' => $e->validator->errors()->first(),
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        }
    }
}
