<?php

namespace Illuminate\Foundation\Auth;

use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Notifications\Notification;

class EmailVerificationRequest extends FormRequest

{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = User::find($this->route('id'));

        $hashEmail = sha1($user->email);

        if (!$user) {
            return false; // ユーザーが存在しない場合、認証失敗
        }

        // ユーザーが自身のIDとメールアドレスのハッシュを持つことを確認するロジック
        if (!hash_equals((string) $user->id, (string) $this->route('id'))) {
            return false; // IDが一致しない場合、認証失敗
        }

        if (!hash_equals($hashEmail, (string) $this->route('hash'))) {
            return false; // ハッシュが一致しない場合、認証失敗
        }

        // ユーザーが正しいIDとハッシュを持ち、認証成功
        return true;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }

    /**
     * Fulfill the email verification request.
     *
     * @return void
     */
    public function fulfill()
    {
        if (!$this->user()->hasVerifiedEmail()) {
            $this->user()->markEmailAsVerified();

            event(new Verified($this->user()));
        }
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        return $validator;
    }
}
