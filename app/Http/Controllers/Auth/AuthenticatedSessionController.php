<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Pipeline;
use Laravel\Fortify\Actions\AttemptToAuthenticate;
use Laravel\Fortify\Actions\CanonicalizeUsername;
use Laravel\Fortify\Actions\EnsureLoginIsNotThrottled;
use Laravel\Fortify\Actions\PrepareAuthenticatedSession;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Contracts\LoginViewResponse;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Http\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends BaseController
{


    public function __construct()
    {
    }

    /**
     * Show the login view.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Laravel\Fortify\Contracts\LoginViewResponse
     */
    public function create(Request $request): LoginViewResponse
    {
        return app(LoginViewResponse::class);
    }

    /**
     * Attempt to authenticate a new session.
     *
     * @param  \Laravel\Fortify\Http\Requests\LoginRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(LoginRequest $request): JsonResponse
    {
        return $this->loginPipeline($request)->then(function ($request) {
            try {
                if (!empty($existOwner)) {
                    $this->responseMan([
                        'message' => 'オーナー用ユーザーとしてログインしました!',
                        'responseUser' => $request->user()->only('id', 'name', 'email', 'phone_number',  'isAttendance'),
                    ]);
                } else {
                    return $this->responseMan([
                        'message' => 'スタッフ用ユーザーとしてログインしました!',
                        'responseUser' => $request->user()->only('id', 'name', 'email', 'phone_number',  'isAttendance'),
                    ]);
                }
            } catch (\Exception $e) {
                // Log::error($e->getMessage());
                return $this->responseMan([
                    'message' =>
                    'ログインに失敗しました。もう一度やり直してください。',
                ], 500);
            }
        });
    }

    /**
     * Get the authentication pipeline instance.
     *
     * @param  \Laravel\Fortify\Http\Requests\LoginRequest  $request
     * @return \Illuminate\Pipeline\Pipeline
     */
    protected function loginPipeline(LoginRequest $request)
    {
        if (Fortify::$authenticateThroughCallback) {
            return (new Pipeline(app()))->send($request)->through(array_filter(
                call_user_func(Fortify::$authenticateThroughCallback, $request)
            ));
        }

        if (is_array(config('fortify.pipelines.login'))) {
            return (new Pipeline(app()))->send($request)->through(array_filter(
                config('fortify.pipelines.login')
            ));
        }

        return (new Pipeline(app()))->send($request)->through(array_filter([
            config('fortify.limiters.login') ? null : EnsureLoginIsNotThrottled::class,
            config('fortify.lowercase_usernames') ? CanonicalizeUsername::class : null,
            Features::enabled(Features::twoFactorAuthentication()) ? RedirectIfTwoFactorAuthenticatable::class : null,
            AttemptToAuthenticate::class,
            PrepareAuthenticatedSession::class,
        ]));
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse
     */
    public function destroy(Request $request): JsonResponse
    {
        try {

            // ユーザーをログアウトさせる
            Auth::guard('web')->logout();

            if ($request->hasSession()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            return $this->responseMan([
                'message' => 'ログアウトしました!'
            ]);
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            return $this->responseMan([
                'message' => 'ログアウトに失敗しました。もう一度やり直してください。',
            ], 500);
        }
    }
}
