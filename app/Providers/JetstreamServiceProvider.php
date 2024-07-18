<?php

namespace App\Providers;

use App\Actions\Jetstream\DeleteUserMain;
use Illuminate\Support\ServiceProvider;
use Laravel\Jetstream\Jetstream;
use App\Enums\Roles;
use App\Enums\Permissions;

class JetstreamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Roles::initialize();

        $this->configurePermissions();

        Jetstream::deleteUsersUsing(DeleteUserMain::class);
    }

    /**
     * Configure the permissions that are available within the application.
     */
    protected function configurePermissions(): void
    {
        Jetstream::defaultApiTokenPermissions([
            Roles::$STAFF,
            Permissions::ALL_PERMISSION,
        ]);

        Jetstream::role(
            Roles::$OWNER,
            'オーナー',
            [
                Permissions::ALL_PERMISSION,
                Permissions::OWNER_PERMISSION,
                Permissions::MANAGER_PERMISSION,
            ]
        )->description('オーナー権限。全ての権限を持つ。');

        Jetstream::role(
            Roles::$MANAGER,
            'マネージャー',
            [
                Permissions::MANAGER_PERMISSION,
                Permissions::ALL_PERMISSION,
            ]
        )->description('マネージャー権限。削除機能とusers編集権限以外の全ての権限を持つ。');

        Jetstream::role(
            Roles::$STAFF,
            'スタッフ',
            [Permissions::ALL_PERMISSION]
        )->description('全員が触れるメソッドしか使えない権限。');
    }
}
