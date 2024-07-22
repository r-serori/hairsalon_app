<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $user = User::find($request->route('id'));

            if (!$user) {
                DB::rollBack();
                return redirect(env('FRONTEND_URL') . '/error?code=404')
                    ->with('status', 'error')
                    ->with('message', 'ユーザーが見つかりません。');
            }

            if ($user->hasVerifiedEmail()) {
                DB::rollBack();
                return redirect(env('FRONTEND_URL') . '/auth/owner')
                    ->with('status', 'success')
                    ->with('message', 'メールアドレスは既に認証済みです！');
            }

            if ($user->markEmailAsVerified()) {
                Log::info('EmailVerificationRequestSuccess: ', ['user' => $user]);
                event(new Verified($user));
                Auth::guard('web')->login($user); // 認証が完了した場合にのみログイン

                // セッションが正しく設定されているか確認
                Log::info('User logged in:', ['user_id' => $user->id, 'is_authenticated' => Auth::check()]);

                DB::commit();
                return redirect(env('FRONTEND_URL') . '/auth/owner')
                    ->with('status', 'success')
                    ->with('message', 'メールアドレスが認証されました！');
            }

            DB::rollBack();
            return redirect(env('FRONTEND_URL') . '/error?code=500')
                ->with('status', 'error')
                ->with('message', 'メールアドレスの認証に失敗しました。');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('EmailVerificationError: ' . $e->getMessage());
            return redirect(env('FRONTEND_URL') . '/error?code=500')
                ->with('status', 'error')
                ->with('message', 'メールアドレスの認証に失敗しました。');
        }
    }
}
