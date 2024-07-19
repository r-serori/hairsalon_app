<?php

namespace App\Services;

use App\Models\User;
use App\Enums\Roles;
use Illuminate\Support\Facades\Auth;


class HasRole
{
    protected $user;

    public function __construct()
    {
        $this->user = User::find(Auth::id());

        if (!$this->user) {
            // 認証されていない場合の処理
            abort(403, '認証されていません。ログインしてください。');
        }
    }
    public function AllAllow(): User

    {
        if ($this->user && $this->user->hasRole(Roles::$OWNER) || $this->user->hasRole(Roles::$MANAGER) || $this->user->hasRole(Roles::$STAFF)) {
            return $this->user;
        } else {
            abort(403, '権限がありません');
        }
    }

    public function ManagerAllow(): User
    {
        if ($this->user && $this->user->hasRole(Roles::$OWNER) || $this->user->hasRole(Roles::$MANAGER)) {
            return $this->user;
        } else {
            abort(403, '権限がありません');
        }
    }

    public function OwnerAllow(): User
    {
        if ($this->user && $this->user->hasRole(Roles::$OWNER)) {
            return $this->user;
        } else {
            abort(403, '権限がありません');
        }
    }
}
