<?php

namespace App\Services;

use App\Models\User;
use App\Enums\Roles;
use Illuminate\Support\Facades\Auth;

class HasRole
{

    public function __construct()
    {
    }
    public function allAllow(): User

    {
        $user = User::find(Auth::id());
        if ($user && $user->hasRole(Roles::$OWNER) || $user->hasRole(Roles::$MANAGER) || $user->hasRole(Roles::$STAFF)) {
            return $user;
        } else {
            abort(403, '権限がありません');
        }
    }

    public function managerAllow(): User
    {
        $user = User::find(Auth::id());
        if ($user && $user->hasRole(Roles::$OWNER) || $user->hasRole(Roles::$MANAGER)) {
            return $user;
        } else {
            abort(403, '権限がありません');
        }
    }

    public function ownerAllow(): User
    {
        $user = User::find(Auth::id());
        if ($user && $user->hasRole(Roles::$OWNER)) {
            return $user;
        } else {
            abort(403, '権限がありません');
        }
    }
}
