<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Models\User;
use App\Models\staff;

class UserGetController extends Controller
{
    public function getUsers(Request $request, $id): JsonResponse
    {
        $staffs = staff::where('owner_id', $id)->get();

        $userIds = $staffs->pluck('user_id')->toArray();

        $users = User::wheeIn('id', $userIds)->get();

        return response()->json([
            'resStatus' => 'success',
            'message' => 'ユーザー情報を取得しました。',
            'responseUsers' => $users->map(function ($user) {
                return $user->only(['id', 'name', 'email', 'phone_number', 'role', 'isAttendance', 'created_at', 'updated_at']);
            })
        ]);
    }
}
