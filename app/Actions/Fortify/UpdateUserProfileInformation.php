<?php

namespace App\Actions\Fortify;

use Illuminate\Support\Facades\Gate;
use App\Enums\Permissions;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Enums\Roles;
use Illuminate\Support\Facades\Auth;


use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use Illuminate\Http\JsonResponse;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  mixed  $user
     * @param  array  $input
     * @return JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update($user, array $input): JsonResponse
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER) || $user->hasRole(Roles::$MANAGER) || $user->hasRole(Roles::$STAFF)) {

                Validator::make($input, [
                    'name' => ['required', 'string', 'max:255'],
                    'email' => ['required', 'email', 'max:255'],
                    'phone_number' => ['required', 'string', 'max:13'],
                ])->validateWithBag('updateProfileInformation');

                if (
                    isset($input['email']) && $user->email !== $input['email'] &&
                    $user instanceof MustVerifyEmail
                ) {
                    $this->updateVerifiedUser($user, $input);

                    return response()->json(
                        [

                            'message' => 'プロフィール情報の更新に成功しました!確認メールを送信しました!',

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
                        'name' => $input['name'],
                        'email' => $input['email'],
                        'phone_number' => $input['phone_number'],
                    ])->save();



                    return response()->json(
                        [
                            'message' => 'プロフィール情報の更新に成功しました!',
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

    /**
     * Update the given verified user's email address.
     *
     * @param  mixed  $user
     * @param  array  $input
     * @return mixed 
     */
    protected function updateVerifiedUser($user, array $input)
    {
        try {
            $user->forceFill([
                'name' => $input['name'],
                'email' => $input['email'],
                'phone_number' => $input['phone_number'],
                'email_verified_at' => null,
            ])->save();

            $user->sendEmailVerificationNotification();

            return $user;
        } catch (\Exception $e) {
            return throw new \Exception($e->getMessage());
        }
    }
}
