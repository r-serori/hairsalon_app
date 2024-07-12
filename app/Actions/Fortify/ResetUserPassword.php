<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use App\Enums\Permissions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\ResetsUserPasswords;
use Illuminate\Support\Facades\Auth;
use App\Enums\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;



class ResetUserPassword implements ResetsUserPasswords
{
    use PasswordValidationRules;


    public function reset($user, array $input)
    {
    }



    public function resetPassword(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'token' => 'required',
                'password' => $this->passwordRules(),
                'password_confirmation' => 'required|same:password',
            ], [
                'password_confirmation.same' => __('パスワードと確認フィールドが一致していません！'), // エラーメッセージ追加
            ]);

            Log::info('パスワードリセットのリクエストを受け付けました！', $request->all());
            Log::info('パスワードリセットのリクエストを受け付けました！', $request->only('email', 'password', 'password_confirmation', 'token'));



            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password),
                    ])->save();
                }
            );



            if ($status === Password::PASSWORD_RESET) {
                return response()->json([
                    'message' => 'パスワードのリセットに成功しました!',
                ], 200);
            } elseif ($status === Password::INVALID_TOKEN) {
                return response()->json([
                    'message' => 'パスワードのリセットに失敗しました。リンクが無効な可能性があります。もう一度お試しください。',
                ], 400);
            } else {
                return response()->json([
                    'message' => 'パスワードのリセットに失敗しました。もう一度お試しください。',
                ], 500);
            }
        } catch (ValidationException $e) {
            Log::error($e->getMessage());
            return response()->json([
                'message' => 'パスワードのリセットに失敗しました！',
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'message' => 'パスワードのリセットに失敗しました！',
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }
}
