<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;


class VerifyEmailController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->user()->sendEmailVerificationNotification(new VerifyEmailNotification);

        return $request->wantsJson()
            ? response()->json(['message' => 'Verification link sent'])
            : back()->with('status', 'verification-link-sent');
    }
}
