<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): JsonResponse
    {
        try {


            if ($request->user()->hasVerifiedEmail()) {
                Log::info('EmailVerificationRequest invoked');
                return response()->json([
                    'status' => 'success',
                    'message' => 'メールアドレスの認証に成功しました！',
                ], 200);
            }



            if ($request->user()->markEmailAsVerified()) {
                Log::info('EmailVerificationRequest invoked');
                event(new Verified($request->user()));
            }


            return response()->json([
                Log::info('EmailVerificationRequest invoked'),
                'status' => 'success',
                'message' => 'メールアドレスの認証に成功しました！',
            ], 200);
        } catch (\Exception $e) {
            Log::error('メッセージエラー', $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'メールアドレスの認証に失敗しました！',
            ], 500);
        }
    }
}
