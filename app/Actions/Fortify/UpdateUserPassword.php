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

class UpdateUserPassword implements UpdatesUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and update the user's password.
     *
     * @param  array<string, string>  $input
     * @return JsonResponse
     */
    public function update($user, array $input): JsonResponse
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER) || $user->hasRole(Roles::$MANAGER) || $user->hasRole(Roles::$STAFF)) {

                // パスワードのバリデーション
                Validator::make($input, [
                    'current_password' => ['required', 'string', 'current_password:web'],
                    'password' => $this->passwordRules(),
                ], [
                    'current_password.current_password' => __('送信されたパスワードが既存のパスワードと一致しません！もう一度試してください！'),
                ])->validateWithBag('updatePassword');

                // パスワードの更新
                $user->forceFill([
                    'password' => Hash::make($input['password']),
                ])->save();

                return response()->json(['status' => 'success', 'message' => 'Password updated successfully.']);
            } else {
                return response()->json([

                    'message' => 'あなたは権限がありません。',
                ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (ValidationException $e) {
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
