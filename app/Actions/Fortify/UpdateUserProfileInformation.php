<?php

namespace App\Actions\Fortify;

use Illuminate\Support\Facades\Gate;
use App\Enums\Permissions;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


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
            if (Gate::allows(Permissions::ALL_PERMISSION)) {

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
                } else {
                    $user->forceFill([
                        'name' => $input['name'],
                        'email' => $input['email'],
                        'phone_number' => $input['phone_number'],
                    ])->save();

                    return response()->json(
                        [
                            'resStatus' => "success",
                            'message' => 'プロフィール情報の更新に成功しました!',
                            'responseUser' => $user->only('id', 'name', 'email', 'phone_number', 'role', 'isAttendance', 'created_at', 'updated_at')
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
                        'resStatus' => "error",
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
                    'resStatus' => "error",
                    'message' => 'プロフィール情報の更新に失敗しました!',
                    'error' => $e->getMessage()
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
     * @return JsonResponse
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

            return response()->json(
                [
                    'resStatus' => "success",
                    'message' => 'プロフィール情報の更新に成功しました!',
                    'responseUser' => $user->only('id', 'name', 'email', 'phone_number', 'role', 'isAttendance', 'created_at', 'updated_at')
                ],
                200,
                [],
                JSON_UNESCAPED_UNICODE
            )->header(
                'Content-Type',
                'application/json; charset=UTF-8'
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'resStatus' => 'error',
                    'message' =>   $e->getMessage()
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
}
