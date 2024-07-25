<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{

    //全てのリクエストに含まれるミドルウェア
    protected $middleware = [
        //ドメイン名を確認して信頼できるかどうかを確認,  app.php, 
        // .envに設定
        //APP_NAME=Laravel
        // APP_ENV=local
        // APP_KEY=base64:9lCEyMrUBYQbdpXP/tpLjH6vK3YgYaqytiY3elN3IuY=
        // APP_DEBUG=true
        // APP_URL=http://localhost　
        \App\Http\Middleware\TrustHosts::class,
        //クライアントとサーバー間の通信をプロキシを使用してフィルタリング、中継役
        //プロキシは無料もあるが、有料のものもある実装段階で選択.envに設定
        // TRUSTED_PROXIES = ~~~.~~~.~~~ 許可するIPアドレスをカンマ区切りで指定
        \App\Http\Middleware\TrustProxies::class,
        //cors.phpを参照して、クロスオリジンリソース共有を設定、セキュリティ強化
        // \Illuminate\Http\Middleware\HandleCors::class,
        //このアプリがメンテナンスモードにある場合、リクエストを拒否
        //1:メンテナンスモードを有効にする:  php artisan down
        // .env ファイルの変更:  APP_ENV=local -> APP_ENV=maintenance
        // メンテナンスモードが終了すると、APP_ENV を再び production や development などの適切な値に戻すことで、通常の運用モードに戻すことができます。
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        //リクエストのサイズを検証、設定されたサイズを超えるリクエストを拒否
        //php.iniのpost_max_sizeの値を取得して、リクエストのサイズを検証,自分で設定することも可能
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        //リクエストの余分な空白文字などを削除するが、passwordなどの例外を設定することも可能
        \App\Http\Middleware\TrimStrings::class,
        //リクエストの空文字をnullに変換する, passwordなどの例外を設定することも可能
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        //フロントエンドとのCORS設定を行うためのミドルウェア
        //FruitCakeライブラリを使用して、CORS設定を行う
    ];

    protected $middlewareGroups = [
        //webミドルウェアグループ
        //csrf保護、セッションの開始、エラーの共有、CSRFトークンの検証、モデルのバインディング
        'web' => [
            //cookieの暗号化,sanctumのtokenなどを暗号化,暗号化しない記述もできる
            \App\Http\Middleware\EncryptCookies::class,
            //cookieの暗号化を行った後、cookieをレスポンスに追加.ブラウザはcookieを保存。次回リクエスト時にcookieを送信
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            //セッションの開始、セッションIDを生成、セッションデータを保存.
            //config/session.phpで設定されたドライバーにセッションデータを保存
            \Illuminate\Session\Middleware\StartSession::class,
            //バックエンドでエラーが発生した場合、エラーメッセージをセッションに保存し、フロントエンドにエラーメッセージをレスポンス
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            //リクエストに含まれるcsrfトークンを検証,csrfトークンが一致しない場合、エラーを返す。　検証しないルートを設定することも可能
            \App\Http\Middleware\VerifyCsrfToken::class,
            //モデルのバインディング,リクエストに含まれるIDをもとにモデルを取得.findメソッドをコントローラーに記述しなくても、モデルを取得できる。
            //RouteServiceProviderのbootメソッドでモデルをバインドすることができる。使用するやつ全部にバインドすることも可能
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            //Inertia.jsのリクエストを処理するためのミドルウェア
            // \App\Http\Middleware\HandleInertiaRequests::class,
            //プリロードされたアセットのリンクヘッダーを追加するためのミドルウェア
            //高速にページを表示するために使用
            // \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
            \App\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ],
        //apiミドルウェアグループ
        //CSRFトークンの検証、モデルのバインディング、フロントエンドリクエストの状態を確認、APIのスロットリング
        'api' => [
            //Sanctumのミドルウェア,フロントエンドリクエストの状態を確認
            //リクエストヘッダーに X-XSRF-TOKEN が存在するかどうかを確認.
            //リクエストが Cookie を要求する場合、その Cookie が Sanctum という名前であることを確認します。
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            //レート制限の設定
            \Illuminate\Routing\Middleware\ThrottleRequests::class,
            //モデルのバインディング
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ],
    ];


    //ルートミドルウェア
    //認証、認可、CSRFトークンの検証、モデルのバインディング、APIのスロットリング
    protected $routeMiddleware = [
        //認証ミドルウェア　認証されていない場合、エラーメッセージを返す
        'auth' => \App\Http\Middleware\Authenticate::class,
        //平文での認証ミドルウェア　危ないので使用しない
        // 'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        //セッションでのユーザー認証の管理　一番強固な認証方法 、　クラス内にエラーが発生しているので、コメントアウト。　見直し必要
        // 'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        //キャッシュをヘッダーに含めるかどうか。ブラウザのキャッシュを有効にする
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        //権限を付与しているかどうかを確認するミドルウェア
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        //login,registerに付与。ログインしている場合、リダイレクト
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        //重要なルートを入れとくとパスワードを再確認するミドルウェア
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        //パスワードリセットリンクなどセキュリティに敏感な操作を行う場合。Laravel の署名付きURL機能を使って生成されたURLが有効期限内であり改ざんされていないことを確認。
        'signed' => \App\Http\Middleware\ValidateSignature::class,
        //レート制限の設定
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
    ];
}
