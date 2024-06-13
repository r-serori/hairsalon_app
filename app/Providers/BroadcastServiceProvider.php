<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */

    //リアルタイムの通知やチャットなどを実装するためのもの
    public function boot()
    {
        Broadcast::routes();

        require base_path('routes/channels.php');
    }
}
