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
use Illuminate\Support\Facades\DB;
use App\Models\Owner;
use App\Models\User;
use App\Enums\Roles;


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

            DB::beginTransaction();
            return $this->loginPipeline($request)->then(function ($request) {
                try {

                    $existOwner = Owner::where('user_id', $request->user()->id)->first();

                    $user = User::find(Auth::id());

                    if ($user->email_verified_at === null) {
                        DB::rollBack();
                        return response()->json([
                            'message' => 'メール認証が完了していません。メール認証を行ってください。',
                        ], 433, [], JSON_UNESCAPED_UNICODE)->header(
                            'Content-Type',
                            'application/json; charset=UTF-8'
                        );
                    }

                    $responseUser =
                        [
                            'id' => $request->user()->id,
                            'name' => $request->user()->name,
                            'email' => $request->user()->email,
                            'phone_number' => $request->user()->phone_number,
                            'role' => $request->user()->role === Roles::$OWNER ? 'オーナー' : ($request->user()->role === Roles::$MANAGER ? 'マネージャー' : 'スタッフ'),
                            'isAttendance' => $request->user()->isAttendance,
                        ];


                    if (!empty($existOwner)) {
                        DB::commit();
                        return $this->responseMan([
                            'ownerRender' => false,
                            'message' => 'オーナー用ユーザーとしてログインしました!',
                            'responseUser' => $responseUser,
                        ]);
                    } else {


                        if ($request->user()->role === Roles::$OWNER) {
                            DB::rollBack();
                            return $this->responseMan([
                                'message' => 'オーナー用ユーザーとしてログインしました!ただし、店舗登録が完了していません。店舗登録を行ってください。',
                                'responseUser' => $responseUser,
                                'ownerRender' => true,
                            ]);
                        } else {

                            DB::commit();

                            return $this->responseMan([
                                'ownerRender' => false,
                                'message' => 'スタッフ用ユーザーとしてログインしました!',
                                'responseUser' => $responseUser,
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    // Log::error($e->getMessage());
                    return response()->json([
                        'message' =>
                        'ログインに失敗しました。もう一度やり直してください。',
                    ], 500);
                }
            });
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
     * @param  \Illuminate\Http\Request  $request {id:id}
     * @return JsonResponse
     */
    public function destroy(Request $request): JsonResponse
    {
        try {
            // ユーザーをログアウトさせる
            $user = User::find($request->id);

            $user->tokens()->delete();
            Auth::guard('web')->logout();

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
