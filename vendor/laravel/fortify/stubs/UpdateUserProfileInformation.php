<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use Nette\Utils\Json;
use Illuminate\Support\Facades\Auth;
use App\Enums\Roles;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, string>  $input
     */
    public function update(array $input): JsonResponse
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER) || $user->hasRole(Roles::$MANAGER) || $user->hasRole(Roles::$STAFF)) {
                Validator::make($input, [
                    'name' => ['required', 'string', 'max:50'],
                    'email' => [
                        'required',
                        'string',
                        'email',
                        'max:200',
                        Rule::unique('users')->ignore($user->id),
                        'phone_number' => 'required|string|max:20',
                    ],
                ])->validateWithBag('updateProfileInformation');

                if (
                    $input['email'] !== $user->email &&
                    $user instanceof MustVerifyEmail
                ) {
                    $this->updateVerifiedUser($user, $input);

                    $responseUser = [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone_number' => $user->phone_number,
                    ];

                    return response()->json([
                        'message' => 'ユーザー情報の更新が完了しました！',
                        'responseUser' => $responseUser
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                } else {
                    $user->forceFill([
                        'name' => $input['name'],
                        'email' => $input['email'],
                        'phone_number' => $input['phone_number'],
                    ])->save();

                    $responseUser = [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone_number' => $user->phone_number,
                    ];

                    return response()->json(
                        [
                            'message' => 'ユーザー情報の更新が完了しました！',
                            'responseUser' => $responseUser
                        ],
                        200,
                        [],
                        JSON_UNESCAPED_UNICODE
                    )->header('Content-Type', 'application/json; charset=UTF-8');
                }
            } else {
                return response()->json(
                    [
                        'message' => '貴方は権限がありません。',
                    ],
                    403,
                    [],
                    JSON_UNESCAPED_UNICODE
                )->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json(
                [
                    'message' => $e->getMessage(),
                ],
                500,
                [],
                JSON_UNESCAPED_UNICODE
            )->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): User | \Exception
    {
        try {
            $user->forceFill([
                'name' => $input['name'],
                'email' => $input['email'],
                'email_verified_at' => null,
                'phone_number' => $input['phone_number'],
            ])->save();

            $user->sendEmailVerificationNotification();

            return $user;
        } catch (\Exception $e) {
            return throw new \Exception('ユーザー情報の更新に失敗しました。');
        }
    }
}
