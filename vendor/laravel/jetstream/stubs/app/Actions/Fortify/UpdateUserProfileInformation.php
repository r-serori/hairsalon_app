<?php

namespace App\Actions\Fortify;

use Illuminate\Support\Facades\Gate;
use App\Enums\Permissions;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Enums\Roles;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Notifications\UpdateProfileNotification;
use Illuminate\Support\Facades\DB;


class UpdateUserProfileInformation
{
    public function update($user, array $input): void
    {
    }



    public function updateUser(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER) || $user->hasRole(Roles::$MANAGER) || $user->hasRole(Roles::$STAFF)) {

                Validator::make($request->all(), [
                    'name' => ['required', 'string', 'max:255'],
                    'email' => ['required', 'email', 'max:255'],
                    'phone_number' => ['required', 'string', 'max:20'],
                ])->validateWithBag('updateProfileInformation');



                if (
                    isset($request->email) && $user->email !== $request->email
                ) {
                    $this->updateVerifiedUserInfo($user, $request);

                    Auth::guard('web')->logout();
                    if ($request->hasSession()) {
                        $request->session()->invalidate();
                        $request->session()->regenerateToken();
                    };

                    DB::commit();


                    return response()->json(
                        [
                            'message' => 'プロフィール情報の更新に成功しました!もう一度ログインしてください！',
                            'redirect' => true
                        ],
                        200,
                        [],
                        JSON_UNESCAPED_UNICODE
                    )->header(
                        'Content-Type',
                        'application/json; charset=UTF-8'
                    );
                } else {
                    $user->forceFill([
                        'name' => $request['name'],
                        'email' => $request['email'],
                        'phone_number' => $request['phone_number'],
                    ])->save();

                    return response()->json(
                        [
                            'message' => 'プロフィール情報の更新に成功しました!',
                            'redirect' => false
                        ],
                        200,
                        [],
                        JSON_UNESCAPED_UNICODE
                    )->header(
                        'Content-Type',
                        'application/json; charset=UTF-8'
                    );
                }
            } else {
                return response()->json(
                    [
                        'message' => 'あなたは権限がありません!',
                    ],
                    403,
                    [],
                    JSON_UNESCAPED_UNICODE
                )->header(
                    'Content-Type',
                    'application/json; charset=UTF-8'
                );
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(
                [
                    'message' => 'プロフィール情報の更新に失敗しました!',

                ],
                500,
                [],
                JSON_UNESCAPED_UNICODE
            )->header(
                'Content-Type',
                'application/json; charset=UTF-8'
            );
        }
    }


    protected function updateVerifiedUserInfo($user, Request $request)
    {
        try {
            // ユーザーのプロフィール情報を更新し、確認メールの送信を行う
            $user->forceFill([
                'name' => $request['name'],
                'email' => $request['email'],
                'phone_number' => $request['phone_number'],
                'email_verified_at' => null, // 確認メールがクリックされるまでは null をセット
            ])->save();

            // メールの送信などの処理を実行する
            $user->notify(new UpdateProfileNotification($user));

            return $user;
        } catch (\Exception $e) {
            // エラー処理
            Log::error($e->getMessage());
            throw new \Exception('プロフィール情報の更新に失敗しました。');
        }
    }
}
