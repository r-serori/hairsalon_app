<?php

namespace Laravel\Fortify\Http\Controllers;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Enums\Permissions;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Pipeline;
use Laravel\Fortify\Actions\AttemptToAuthenticate;
use Laravel\Fortify\Actions\CanonicalizeUsername;
use Laravel\Fortify\Actions\EnsureLoginIsNotThrottled;
use Laravel\Fortify\Actions\PrepareAuthenticatedSession;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\LoginViewResponse;
use Laravel\Fortify\Contracts\LogoutResponse;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Http\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;
use App\Models\Owner;
use App\Models\Staff;
use Illuminate\Support\Facades\Log;

class AuthenticatedSessionController extends Controller
{
    /**
     * The guard implementation.
     *
     * @var \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected $guard;

    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Contracts\Auth\StatefulGuard  $guard
     * @return void
     */
    public function __construct(StatefulGuard $guard)
    {
        $this->guard = $guard;
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

                $existOwner = Owner::where('user_id', $request->user()->id)->first();

                if (!empty($existOwner)) {
                    return response()->json([
                        'message' => 'オーナー用ユーザーとしてログインしました!',
                        'responseOwnerId' => $existOwner->id,
                        'responseUser' => $request->user()->only('id', 'name', 'email', 'phone_number', 'role', 'isAttendance'),
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                } else {
                    $staff = Staff::where('user_id', $request->user()->id)->first();

                    $owner = Owner::find($staff->owner_id);

                    return response()->json([
                        'message' => 'スタッフ用ユーザーとしてログインしました!',
                        'responseOwnerId' => $owner->id,
                        'responseUser' => $request->user()->only('id', 'name', 'email', 'phone_number', 'role', 'isAttendance'),
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }
            } catch (\Exception $e) {
                // Log::error($e->getMessage());
                return response()->json(
                    [
                        'message' =>
                        'ログインに失敗しました。もう一度やり直してください。',
                    ],
                    500,
                    [],
                    JSON_UNESCAPED_UNICODE
                )->header('Content-Type', 'application/json; charset=UTF-8');
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

            $this->guard->logout();

            if ($request->hasSession()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            return response()->json([
                'message' => 'ログアウトしました!'
            ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            return response()->json([
                'message' => 'ログアウトに失敗しました。もう一度やり直してください。',
            ], 500);
        }
    }
}
