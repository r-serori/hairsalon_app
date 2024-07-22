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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UpdateUserInfoController extends Controller
{
  /**
   * Mark the authenticated user's email address as verified.
   */
  public function updateInfoVerifyEmail(EmailVerificationRequest $request): RedirectResponse
  {
    DB::beginTransaction();
    try {
      Log::info('EmailVerificationRequest: ', [$request]);

      $user = User::find($request->route('id'));

      if ($user->hasVerifiedEmail()) {
        DB::rollBack();
        return redirect(env('FRONTEND_URL') . '/auth/login')
          ->with('status', 'success')
          ->with('message', 'メールアドレスは既に認証済みです！');
      }

      if ($user->markEmailAsVerified()) {
        event(new Verified($user));
        DB::commit();

        return redirect(env('FRONTEND_URL') . '/auth/login')
          ->with('status', 'success')
          ->with('message', 'メールアドレスは既に認証済みです！');
      } else {
        DB::rollBack();
        return redirect(env('FRONTEND_URL') . '/error?code=500')
          ->with('status', 'error')
          ->with('message', 'メールアドレスの認証に失敗しました。');
      }
    } catch (\Exception $e) {
      DB::rollBack();
      return redirect(env('FRONTEND_URL') . '/error?code=500')
        ->with('status', 'error')
        ->with('message', 'メールアドレスの認証に失敗しました。');
    }
  }
}
