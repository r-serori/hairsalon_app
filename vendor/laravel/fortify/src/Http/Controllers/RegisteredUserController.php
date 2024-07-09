<?php

namespace Laravel\Fortify\Http\Controllers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\RegisterViewResponse;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\Log;

class RegisteredUserController extends Controller
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
     * Show the registration view.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Laravel\Fortify\Contracts\RegisterViewResponse
     */
    public function create(Request $request): RegisterViewResponse
    {
        return app(RegisterViewResponse::class);
    }

    /**
     * Create a new registered user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Fortify\Contracts\CreatesNewUsers  $creator
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(
        Request $request,
        CreatesNewUsers $creator
    ): JsonResponse {
        try {

            if (config('fortify.lowercase_usernames')) {
                $request->merge([
                    Fortify::username() => Str::lower($request->{Fortify::username()}),
                ]);
            }

            event(new Registered($user = $creator->create($request->all())));

            $user->notify(new \App\Notifications\VerifyEmailNotification($user));

            $this->guard->login($user);

            $responseUser =
                [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone_number' => $user->phone_number,
                    'role' => 'オーナー',
                    'isAttendance' => $user->isAttendance,
                ];


            return response()->json([

                'message' => 'ユーザー登録に成功しました！オーナー登録をしてください！',
                'responseUser' => $responseUser
            ], 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            if (strpos($e->getMessage(), 'メールアドレスの値は既に存在') !== false) {
                return response()->json([
                    'message' => 'メールアドレスが既に存在しています！他のメールアドレスを入力してください！'
                ], 400);
            } else {
                // その他のエラー処理
                return response()->json([
                    'message' => '何らかのエラーが発生しました。もう一度最初からやり直してください！'
                ], 500);
            }
        }
    }
}
