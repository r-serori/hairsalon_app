<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log; // Add this line
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Features;


class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): JsonResponse
    {
        try {
            $validator = Validator::make($input, [
                'name' => ['required', 'string', 'max:50'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'phone_number' => ['required', 'string', 'max:13'],
                'password' => ['required'],
                'role' => ['required', 'string', 'max:10'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'resStatus' => 'error',
                    'message' => '入力内容をご確認ください。',
                    'errors' => $validator->errors()->first(),
                ], 400);
            }
            $existUser = User::where('email', $input['email'])->first();

            if ($existUser) {
                return response()->json([
                    'resStatus' => 'error',
                    'message' => '既にこのメールアドレスは使用されています。'
                ], 400);
            } else {
                $user = User::create([
                    'name' => $input['name'],
                    'email' => $input['email'],
                    'phone_number' => $input['phone_number'],
                    'password' => Hash::make($input['password']),
                    'role' => $input['role'],
                ]);

                if (Features::enabled(Features::registration())) {
                    event(new Registered($user));
                }

                return $user;
            }
        } catch (\Exception $e) {
            Log::error('ユーザー登録処理中にエラーが発生しました。');
            Log::error('エラー内容: ' . $e);
            return response()->json([
                "resStatus" => 'error',
                'message' => 'ユーザー登録に失敗しました。',
            ], 400);
        }
    }
}
