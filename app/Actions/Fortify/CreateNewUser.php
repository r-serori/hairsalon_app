<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
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
        Validator::make($input, [
            'name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['required', 'string', 'max:13'],
            'password' => [
                'required',
            ],
            'role' => ['required', 'string', 'max:10'],
            //利用規約への同意
            // 'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

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

            return response()->json([
                'resStatus' => 'success',
                'message' => 'ユーザー登録が完了しました!'
            ], 200);
        }
    }
}
