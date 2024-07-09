<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        try {
            Log::info('EmailVerificationRequest: ', [$request]);

            $user = User::find($request->route('id'));

            if ($user->hasVerifiedEmail()) {
                return redirect(env('FRONTEND_URL') . '/auth/owner')
                    ->with('status', 'success')
                    ->with('message', 'メールアドレスは既に認証済みです！');
            }


            if ($user->markEmailAsVerified()) {
                event(new Verified($user));
            }

            return redirect(env('FRONTEND_URL') . '/auth/owner')
                ->with('status', 'success')
                ->with('message', 'メールアドレスは既に認証済みです！');
        } catch (\Exception $e) {
            return redirect(env('FRONTEND_URL') . '/_error')
                ->with('status', 'error')
                ->with('message', 'メールアドレスの認証に失敗しました。');
        }
    }
}
