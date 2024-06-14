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
    public function create(array $input): User
    {
        Log::info('ユーザー登録処理を開始します。', $input);
        try {
            $validator = Validator::make($input, [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255'],
                'phone_number' => ['required', 'string', 'max:255'],
                'password' => $this->passwordRules(),
                'role' => ['required', 'string', 'max:255'],
                'isAttendance' => ['required', 'boolean'],
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                throw new \Exception('バリデーションエラー: ' . implode(', ', $errors));
            }
            $existUser = User::where('email', $input['email'])->first();

            if ($existUser) {
                throw new \Exception('このメールアドレスは既に登録されています。');
            } else {
                $user = User::create([
                    'name' => $input['name'],
                    'email' => $input['email'],
                    'phone_number' => $input['phone_number'],
                    'password' => Hash::make($input['password']),
                    'role' => $input['role'],
                    'isAttendance' => $input['isAttendance'],
                ]);

                if (Features::enabled(Features::registration())) {
                    event(new Registered($user));
                }

                return $user;
            }
        } catch (\Exception $e) {
            Log::error('ユーザー登録処理中にエラーが発生しました。');
            Log::error('エラー内容: ' . $e);
            throw $e;
        }
    }
}
