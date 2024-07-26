<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Illuminate\Support\Facades\Log;
use App\Enums\Roles;
use Illuminate\Support\Facades\DB;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     * @return User
     */
    public function create(array $input): User
    {
        Log::info('ユーザー登録処理を開始します。', $input);
        DB::beginTransaction();
        try {
            $validator = Validator::make($input, [
                'name' => 'required | string | max:100',
                'email' => 'required | string | email |max:200 | unique:users',
                'phone_number' => 'required | string | max:20',
                'password' => $this->passwordRules(),
                'role' => 'required | string | max:30',
                'isAttendance' => 'required|boolean'
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                throw new \Exception('バリデーションエラー: ' . implode(', ', $errors));
            }

            // ユーザーを作成
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'phone_number' => $input['phone_number'],
                'password' => Hash::make($input['password']),
                'role' =>  Roles::$OWNER,
                'isAttendance' => $input['isAttendance'],
                'email_verified_at' => null,
            ]);

            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ユーザー登録処理中にエラーが発生しました。');
            Log::error('エラー内容: ' . $e);
            return throw new \Exception($e->getMessage());
        }
    }
}
