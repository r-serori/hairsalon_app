<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Contracts\ResetsUserPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ResetUserPassword implements ResetsUserPasswords
{
    use PasswordValidationRules;


    public function reset($user, array $input)
    {
    }



    public function resetPassword(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'email' => 'required|email|max:200|exists:users,email',
                'token' => 'required|string',
                'password' => $this->passwordRules(),
                'password_confirmation' => ['required', 'string'],
            ], [
                'password_confirmation.same' => __('パスワードと確認フィールドが一致していません！'), // エラーメッセージ追加
            ]);

            $passwordReset = PasswordReset::where('email', $request->email)->where('token', $request->token)->first();

            // Log::info('パスワードリセットのリクエストを受け付けました！', $passwordReset);

            if (empty($passwordReset)) {
                return response()->json([
                    'message' => 'パスワードのリセットに失敗しました。リンクが無効な可能性があります。もう一度お試しください。',
                ], 400);
            } else {
                $user = User::where('email', $request->email)->first();
                $user->password = Hash::make($request->password);
                $user->save();
                $passwordReset->delete();


                // ユーザーをログアウトさせる
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                DB::commit();

                return response()->json([
                    'message' => 'パスワードのリセットに成功しました！',
                ]);
            }
        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json([
                'message' => 'パスワードのリセットに失敗しました！',
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json([
                'message' => 'パスワードのリセットに失敗しました！',
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }
}
