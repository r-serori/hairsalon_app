<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use App\Enums\Permissions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\ResetsUserPasswords;


class ResetUserPassword implements ResetsUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and reset the user's forgotten password.
     *
     * @param  array<string, string>  $input
     */
    public function reset($user, array $input): JsonResponse
    {
        try {
            if (Gate::allows(Permissions::ALL_PERMISSION)) {

                Validator::make($input, [
                    'password' => $this->passwordRules(),
                ])->validate();

                $user->forceFill([
                    'password' => Hash::make($input['password']),
                ])->save();

                return response()->json([
                    'resStatus' => 'success',
                    'message' => 'パスワードのリセットに成功しました!'
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    'resStatus' => 'error',
                    'message' => 'あなたは権限がありません。',
                ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                'resStatus' => 'error',
                'message' => 'エラーが発生しました。もう一度やり直してください。',
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }
}
